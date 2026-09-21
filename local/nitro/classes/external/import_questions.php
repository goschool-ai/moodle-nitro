<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_nitro\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_nitro\local\access;
use local_nitro\local\dry_run;
use local_nitro\local\qbank;

/**
 * Tool import_questions: GIFT or Moodle XML into a named category, through Moodle's importers.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_questions extends external_api {
    /** @var string[] Supported formats and their importer classes. */
    private const FORMATS = ['gift' => 'qformat_gift', 'xml' => 'qformat_xml'];

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'format' => new external_value(PARAM_ALPHA, 'gift or xml (Moodle XML)'),
            'questions' => new external_value(PARAM_RAW, 'The questions in that format. GIFT: one question per '
                . 'block, blocks separated by a blank line, for example "::Q1:: 2+2? {=4 ~3 ~5}".'),
            'category' => new external_value(PARAM_TEXT, 'Category name, for example "Week 4"; created if missing'),
            'quiz_cmid' => new external_value(PARAM_INT, 'Import into this quiz\'s own bank instead of the course\'s '
                . 'shared bank; 0 for the shared bank', VALUE_DEFAULT, 0),
            'dry_run' => dry_run::param(),
        ]);
    }

    /**
     * Imports the questions; all or nothing.
     *
     * @param int $courseid
     * @param string $format
     * @param string $questions
     * @param string $category
     * @param int $quizcmid
     * @param bool $dryrun
     * @return array
     */
    public static function execute(
        int $courseid,
        string $format,
        string $questions,
        string $category,
        int $quizcmid = 0,
        bool $dryrun = false
    ): array {
        global $CFG;
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'format' => $format, 'questions' => $questions, 'category' => $category,
            'quiz_cmid' => $quizcmid, 'dry_run' => $dryrun,
        ]);
        qbank::require_moodle_5();
        access::require_course($params['courseid']);
        $format = strtolower($params['format']);
        if (!isset(self::FORMATS[$format])) {
            throw new \invalid_parameter_exception('format must be gift or xml.');
        }
        if (trim($params['category']) === '') {
            throw new \invalid_parameter_exception('category must not be empty.');
        }
        require_once($CFG->libdir . '/questionlib.php');
        require_once($CFG->dirroot . '/question/format.php');
        require_once($CFG->dirroot . "/question/format/{$format}/format.php");

        return dry_run::run($params['dry_run'], $params['courseid'], function (bool $dryrun) use ($params, $format) {
            global $DB;
            // All or nothing: the bank (if this creates it), the category and the questions share one transaction.
            $transaction = $DB->start_delegated_transaction();
            try {
                $bank = qbank::bank($params['courseid'], $params['quiz_cmid']);
                require_capability('moodle/question:add', $bank);
                [$category, $created] = qbank::category($bank, $params['category'], true);

                $file = make_request_directory() . '/questions.' . ($format === 'xml' ? 'xml' : 'txt');
                file_put_contents($file, $params['questions']);

                $class = self::FORMATS[$format];
                $importer = new $class();
                $importer->setCategory($category);
                $importer->setContexts([$bank]);
                $importer->setCourse(get_course($params['courseid']));
                $importer->setFilename($file);
                $importer->setRealfilename(basename($file));
                $importer->setMatchgrades('error');
                $importer->setCatfromfile(false);
                $importer->setContextfromfile(false);
                $importer->setStoponerror(true);

                // The importer reports progress and errors as HTML output; keep it out of the response.
                ob_start();
                try {
                    $ok = $importer->importpreprocess() && $importer->importprocess() && $importer->importpostprocess();
                } finally {
                    $output = ob_get_clean();
                }
                if (!$ok) {
                    throw new \moodle_exception('importfailed', 'local_nitro', '', self::errors($output, $params['questions']));
                }
                $transaction->allow_commit();
            } catch (\Throwable $e) {
                $transaction->rollback($e);
            }

            $imported = [];
            if ($importer->questionids) {
                [$insql, $inparams] = $DB->get_in_or_equal($importer->questionids);
                foreach ($DB->get_records_select('question', "id {$insql}", $inparams, 'id', 'id, name, qtype') as $q) {
                    // A dry run rolls the import back; the IDs it produced belong to no question.
                    $imported[] = ['id' => $dryrun ? 0 : (int) $q->id, 'name' => $q->name, 'type' => $q->qtype];
                }
            }
            return [
                'bank' => qbank::bank_name($bank),
                'category' => [
                    'id' => $dryrun && $created ? 0 : (int) $category->id,
                    'name' => $category->name,
                    'created' => $created,
                ],
                'count' => count($imported),
                'questions' => $imported,
            ];
        });
    }

    /**
     * Turns the importer's HTML output into error messages with the line of each faulty question.
     *
     * @param string $output
     * @param string $input
     * @return string
     */
    private static function errors(string $output, string $input): string {
        $messages = [];
        if (preg_match_all('~<div class="importerror">(.*?)</div>~s', $output, $blocks)) {
            foreach ($blocks[1] as $block) {
                $quoted = preg_match('~<blockquote>(.*?)</blockquote>~s', $block, $m)
                    ? html_entity_decode($m[1], ENT_QUOTES) : '';
                $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(
                    preg_replace('~<blockquote>.*?</blockquote>~s', ' ', $block)
                ), ENT_QUOTES)));
                $pos = $quoted !== '' ? strpos($input, trim(strtok($quoted, "\n"))) : false;
                $line = $pos === false ? '' : ' (line ' . (substr_count(substr($input, 0, $pos), "\n") + 1) . ')';
                $snippet = $quoted !== '' ? ': "' . \core_text::substr(trim($quoted), 0, 80) . '"' : '';
                $messages[] = $text . $line . $snippet;
            }
        }
        if (!$messages) {
            $messages[] = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($output), ENT_QUOTES)));
        }
        return implode(' | ', $messages);
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'dry_run' => new external_value(PARAM_BOOL, 'True if nothing was imported'),
            'dry_run_note' => dry_run::note_returns(),
            'bank' => new external_value(PARAM_TEXT, 'Question bank'),
            'category' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Category ID'),
                'name' => new external_value(PARAM_TEXT, 'Category name'),
                'created' => new external_value(PARAM_BOOL, 'The category was created by this import'),
            ]),
            'count' => new external_value(PARAM_INT, 'Number of questions imported'),
            'questions' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Question ID, for add_questions_to_quiz; 0 after a dry '
                    . 'run, because the question was not imported'),
                'name' => new external_value(PARAM_TEXT, 'Question name'),
                'type' => new external_value(PARAM_PLUGIN, 'Question type'),
            ])),
        ]);
    }
}
