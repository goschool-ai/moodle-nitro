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
use core_external\external_single_structure;
use core_external\external_value;
use local_nitro\local\access;
use local_nitro\local\content;
use local_nitro\local\dates;
use local_nitro\local\dry_run;
use local_nitro\local\modules;
use local_nitro\local\qbank;
use mod_quiz\question\display_options;

/**
 * Tool create_quiz: a Quiz with timing, attempts, shuffling and a review-options preset.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_quiz extends external_api {
    /** @var string[] Review items of a quiz. */
    private const REVIEW_ITEMS = ['attempt', 'correctness', 'maxmarks', 'marks', 'specificfeedback', 'generalfeedback',
        'rightanswer', 'overallfeedback'];

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'name' => new external_value(PARAM_TEXT, 'Quiz name'),
            'intro' => new external_value(PARAM_RAW, 'Introduction in markdown', VALUE_DEFAULT, ''),
            'section' => new external_value(PARAM_INT, 'Section number', VALUE_DEFAULT, 0),
            'key' => new external_value(PARAM_RAW, 'Optional stable key (ID number) of the quiz', VALUE_DEFAULT, ''),
            'open' => new external_value(PARAM_RAW, 'Opens at, ISO 8601; empty for open now', VALUE_DEFAULT, ''),
            'close' => new external_value(PARAM_RAW, 'Closes at, ISO 8601; empty for never', VALUE_DEFAULT, ''),
            'time_limit_minutes' => new external_value(PARAM_INT, 'Time limit in minutes; 0 for none', VALUE_DEFAULT, 0),
            'attempts' => new external_value(PARAM_INT, 'Attempts allowed; 0 for unlimited', VALUE_DEFAULT, 0),
            'shuffle_questions' => new external_value(
                PARAM_BOOL,
                'Shuffle question order for each attempt',
                VALUE_DEFAULT,
                false
            ),
            'review' => new external_value(PARAM_ALPHA, 'practice: answers and feedback shown right after each '
                . 'attempt; exam: shown only after the quiz closes', VALUE_DEFAULT, 'practice'),
            'visible' => new external_value(
                PARAM_BOOL,
                'Visible to students. Hidden is safer until questions are added.',
                VALUE_DEFAULT,
                false
            ),
            'dry_run' => dry_run::param(),
        ]);
    }

    /**
     * Creates the quiz.
     *
     * @param int $courseid
     * @param string $name
     * @param string $intro
     * @param int $section
     * @param string $key
     * @param string $open
     * @param string $close
     * @param int $timelimitminutes
     * @param int $attempts
     * @param bool $shufflequestions
     * @param string $review
     * @param bool $visible
     * @param bool $dryrun
     * @return array
     */
    public static function execute(
        int $courseid,
        string $name,
        string $intro = '',
        int $section = 0,
        string $key = '',
        string $open = '',
        string $close = '',
        int $timelimitminutes = 0,
        int $attempts = 0,
        bool $shufflequestions = false,
        string $review = 'practice',
        bool $visible = false,
        bool $dryrun = false
    ): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/quiz/lib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'name' => $name, 'intro' => $intro, 'section' => $section, 'key' => $key,
            'open' => $open, 'close' => $close, 'time_limit_minutes' => $timelimitminutes, 'attempts' => $attempts,
            'shuffle_questions' => $shufflequestions, 'review' => $review, 'visible' => $visible, 'dry_run' => $dryrun,
        ]);
        qbank::require_moodle_5();
        $context = access::require_course($params['courseid']);
        require_capability('moodle/course:manageactivities', $context);
        modules::check_section($params['courseid'], $params['section']);
        if (!in_array($params['review'], ['practice', 'exam'], true)) {
            throw new \invalid_parameter_exception('review must be practice or exam.');
        }
        if ($params['attempts'] < 0 || $params['time_limit_minutes'] < 0) {
            throw new \invalid_parameter_exception('attempts and time_limit_minutes must not be negative.');
        }
        $key = trim($params['key']);
        if ($key !== '' && modules::find_by_key($params['courseid'], $key)) {
            throw new \invalid_parameter_exception("The key '{$key}' is already used in this course.");
        }
        $timeopen = $params['open'] === '' ? 0 : dates::parse($params['open'], 'open');
        $timeclose = $params['close'] === '' ? 0 : dates::parse($params['close'], 'close');
        if ($timeopen && $timeclose && $timeclose <= $timeopen) {
            throw new \invalid_parameter_exception('close must be after open.');
        }
        $converted = content::from_markdown($params['intro']);

        return dry_run::run($params['dry_run'], $params['courseid'], function (bool $dryrun) use (
            $params,
            $key,
            $timeopen,
            $timeclose,
            $converted
) {
            global $DB;
            $data = self::defaults();
            $data->modulename = 'quiz';
            $data->course = $params['courseid'];
            $data->section = $params['section'];
            $data->visible = (int) $params['visible'];
            $data->cmidnumber = $key;
            $data->name = $params['name'];
            $data->introeditor = ['text' => $converted['html'], 'format' => FORMAT_HTML, 'itemid' => 0];
            $data->timeopen = $timeopen;
            $data->timeclose = $timeclose;
            $data->timelimit = $params['time_limit_minutes'] * MINSECS;
            $data->attempts = $params['attempts'];
            self::apply_review_preset($data, $params['review']);

            $cm = modules::create($data);
            $quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
            $DB->set_field(
                'quiz_sections',
                'shufflequestions',
                (int) $params['shuffle_questions'],
                ['quizid' => $quiz->id, 'firstslot' => 1]
            );

            return [
                // A dry run rolls the quiz back, so its ID and URL would only mislead.
                'cmid' => $dryrun ? 0 : (int) $cm->id,
                'key' => $key,
                'url' => $dryrun ? '' : $cm->url->out(false),
                'name' => $cm->get_formatted_name(),
                'section' => (int) $cm->sectionnum,
                'visible' => (bool) $cm->visible,
                'open' => dates::describe((int) $quiz->timeopen),
                'close' => dates::describe((int) $quiz->timeclose),
                'time_limit_minutes' => (int) ($quiz->timelimit / MINSECS),
                'attempts' => (int) $quiz->attempts,
                'shuffle_questions' => (bool) $params['shuffle_questions'],
                'review' => $params['review'],
                'grading_method' => (int) $quiz->grademethod,
                'max_grade' => (float) $quiz->grade,
                'removed_by_cleaning' => $converted['removed'],
            ];
        });
    }

    /**
     * Form data for a new quiz: the quiz table defaults, then the site's quiz defaults.
     *
     * @return \stdClass
     */
    private static function defaults(): \stdClass {
        global $DB;
        $data = new \stdClass();
        foreach ($DB->get_columns('quiz') as $name => $column) {
            if ($name !== 'id' && $column->has_default) {
                $data->$name = $column->default_value;
            }
        }
        $config = (array) get_config('quiz');
        foreach ($data as $name => $value) {
            if (array_key_exists($name, $config) && !str_starts_with($name, 'review')) {
                $data->$name = $config[$name];
            }
        }
        $data->grade = $config['maximumgrade'] ?? 10;
        $data->quizpassword = '';
        $data->subnet = '';
        $data->browsersecurity = $config['browsersecurity'] ?? '-';
        $data->completionminattempts = 0;
        $data->completionattemptsexhausted = 0;
        return $data;
    }

    /**
     * Sets the review options the edit form would submit for a preset.
     *
     * practice: everything is shown right after each attempt, while the quiz is open and after it closes.
     * exam: only whether the attempt exists is shown until the quiz closes; then everything.
     *
     * @param \stdClass $data
     * @param string $preset
     */
    private static function apply_review_preset(\stdClass $data, string $preset): void {
        $whens = ['during', 'immediately', 'open', 'closed'];
        foreach (self::REVIEW_ITEMS as $item) {
            foreach ($whens as $when) {
                $show = match ($preset) {
                    'practice' => $when !== 'during' || $item === 'attempt',
                    default => $when === 'closed' || ($item === 'attempt' && $when === 'during'),
                };
                $data->{$item . $when} = (int) $show;
            }
        }
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'dry_run' => new external_value(PARAM_BOOL, 'True if nothing was created'),
            'dry_run_note' => dry_run::note_returns(),
            'cmid' => new external_value(PARAM_INT, 'Course module ID of the quiz, for add_questions_to_quiz; '
                . '0 after a dry run, because the quiz was not created'),
            'key' => new external_value(PARAM_RAW, 'Key (ID number), if given'),
            'url' => new external_value(PARAM_URL, 'Quiz URL'),
            'name' => new external_value(PARAM_TEXT, 'Name'),
            'section' => new external_value(PARAM_INT, 'Section number'),
            'visible' => new external_value(PARAM_BOOL, 'Visible to students'),
            'open' => dates::describe_returns('Opens'),
            'close' => dates::describe_returns('Closes'),
            'time_limit_minutes' => new external_value(PARAM_INT, 'Time limit in minutes; 0 for none'),
            'attempts' => new external_value(PARAM_INT, 'Attempts allowed; 0 for unlimited'),
            'shuffle_questions' => new external_value(PARAM_BOOL, 'Question order shuffled'),
            'review' => new external_value(PARAM_ALPHA, 'Review preset'),
            'grading_method' => new external_value(PARAM_INT, 'Grading method (1 highest, 2 average, 3 first, 4 last)'),
            'max_grade' => new external_value(PARAM_FLOAT, 'Maximum grade of the quiz'),
            'removed_by_cleaning' => new \core_external\external_multiple_structure(
                new external_value(PARAM_RAW, 'Removed item'),
                'Removed from the introduction by HTML cleaning'
            ),
        ]);
    }
}
