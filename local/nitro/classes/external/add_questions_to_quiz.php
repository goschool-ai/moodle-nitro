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
use mod_quiz\quiz_settings;

/**
 * Tool add_questions_to_quiz: fixed questions by ID and random questions from a category.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_questions_to_quiz extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quiz_cmid' => new external_value(PARAM_INT, 'Course module ID of the quiz, from create_quiz or '
                . 'course_overview'),
            'question_ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Question ID'),
                'Specific questions to add, by the IDs import_questions returned',
                VALUE_DEFAULT,
                []
            ),
            'random_category' => new external_value(
                PARAM_TEXT,
                'Category to draw random questions from, by name',
                VALUE_DEFAULT,
                ''
            ),
            'random_count' => new external_value(PARAM_INT, 'How many random questions to add', VALUE_DEFAULT, 0),
            'random_from_quiz_bank' => new external_value(PARAM_BOOL, 'The random category is in the quiz\'s own bank '
                . '(default: the course\'s shared bank)', VALUE_DEFAULT, false),
            'mark' => new external_value(PARAM_FLOAT, 'Mark of each added question', VALUE_DEFAULT, 1),
            'questions_per_page' => new external_value(PARAM_INT, 'Repaginate the quiz with this many questions per '
                . 'page; 0 to leave the paging as it is', VALUE_DEFAULT, 0),
            'max_grade' => new external_value(
                PARAM_FLOAT,
                'Maximum grade of the quiz; 0 (the default) makes it equal to what the questions are worth '
                    . 'together, so students see the marks you set here',
                VALUE_DEFAULT,
                0
            ),
            'dry_run' => dry_run::param(),
        ]);
    }

    /**
     * Adds the questions.
     *
     * @param int $quizcmid
     * @param array $questionids
     * @param string $randomcategory
     * @param int $randomcount
     * @param bool $randomfromquizbank
     * @param float $mark
     * @param int $questionsperpage
     * @param float $maxgrade
     * @param bool $dryrun
     * @return array
     */
    public static function execute(
        int $quizcmid,
        array $questionids = [],
        string $randomcategory = '',
        int $randomcount = 0,
        bool $randomfromquizbank = false,
        float $mark = 1,
        int $questionsperpage = 0,
        float $maxgrade = 0,
        bool $dryrun = false
    ): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        require_once($CFG->libdir . '/questionlib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'quiz_cmid' => $quizcmid, 'question_ids' => $questionids, 'random_category' => $randomcategory,
            'random_count' => $randomcount, 'random_from_quiz_bank' => $randomfromquizbank, 'mark' => $mark,
            'questions_per_page' => $questionsperpage, 'max_grade' => $maxgrade, 'dry_run' => $dryrun,
        ]);
        qbank::require_moodle_5();
        $context = access::require_module($params['quiz_cmid']);
        require_capability('mod/quiz:manage', $context);
        [$course, $cm] = get_course_and_cm_from_cmid($params['quiz_cmid'], 'quiz');
        $quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);

        if (!$params['question_ids'] && !$params['random_count']) {
            throw new \invalid_parameter_exception('Give question_ids, or random_category with random_count, or both.');
        }
        if ($params['mark'] <= 0) {
            throw new \invalid_parameter_exception('mark must be positive.');
        }
        if ($params['max_grade'] < 0) {
            throw new \invalid_parameter_exception('max_grade must not be negative.');
        }
        if (quiz_has_attempts($quiz->id)) {
            throw new \moodle_exception('quizattempted', 'local_nitro');
        }

        // Fixed questions: each must exist and be usable from its bank.
        $questions = [];
        foreach (array_unique($params['question_ids']) as $id) {
            $question = $DB->get_record('question', ['id' => $id], 'id, name, qtype');
            if (!$question) {
                throw new \invalid_parameter_exception("There is no question with ID {$id}.");
            }
            $bankcontext = \context::instance_by_id($DB->get_field_sql('SELECT qc.contextid
                  FROM {question_versions} qv
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qv.questionid = ?', [$id], MUST_EXIST));
            require_capability('moodle/question:useall', $bankcontext);
            $questions[] = $question;
        }

        // Random questions: the category must hold enough.
        $category = null;
        if ($params['random_count'] > 0) {
            $bank = qbank::bank((int) $course->id, $params['random_from_quiz_bank'] ? (int) $cm->id : 0);
            require_capability('moodle/question:useall', $bank);
            [$category] = qbank::category($bank, $params['random_category'], false);
            if (!$category) {
                throw new \invalid_parameter_exception("There is no category called '{$params['random_category']}' in "
                    . qbank::bank_name($bank) . '. Import questions into it first with import_questions.');
            }
            $available = qbank::count_ready((int) $category->id);
            if ($params['random_count'] > $available) {
                throw new \moodle_exception(
                    'notenoughquestions',
                    'local_nitro',
                    '',
                    ['category' => $category->name, 'available' => $available, 'requested' => $params['random_count']]
                );
            }
        }

        $write = function (bool $dryrun) use ($params, $quiz, $questions, $category) {
            global $DB;
            $settings = quiz_settings::create($quiz->id);
            $structure = $settings->get_structure();
            $lastpage = max(1, (int) $DB->get_field_sql('SELECT MAX(page) FROM {quiz_slots} WHERE quizid = ?', [$quiz->id]));
            foreach ($questions as $question) {
                quiz_add_quiz_question($question->id, $quiz, $lastpage, $params['mark']);
            }
            if ($category) {
                $filter = ['filter' => ['category' => [
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                    'values' => [$category->id],
                    'filteroptions' => ['includesubcategories' => false],
                ]]];
                $before = $DB->get_fieldset('quiz_slots', 'id', ['quizid' => $quiz->id]);
                $structure->add_random_questions($lastpage, $params['random_count'], $filter);
                $structure = quiz_settings::create($quiz->id)->get_structure();
                foreach ($DB->get_records('quiz_slots', ['quizid' => $quiz->id]) as $slot) {
                    if (!in_array($slot->id, $before) && (float) $params['mark'] !== 1.0) {
                        $structure->update_slot_maxmark($slot, $params['mark']);
                    }
                }
            }
            if ($params['questions_per_page'] > 0) {
                quiz_repaginate_questions($quiz->id, $params['questions_per_page']);
                $DB->set_field('quiz', 'questionsperpage', $params['questions_per_page'], ['id' => $quiz->id]);
            }
            $settings = quiz_settings::create($quiz->id);
            $calculator = $settings->get_grade_calculator();
            $calculator->recompute_quiz_sumgrades();
            $quiz = $DB->get_record('quiz', ['id' => $quiz->id], '*', MUST_EXIST);
            // Without this the quiz keeps its default maximum while the questions are worth something else,
            // and Moodle scales every mark. Quizzes with attempts are refused above, so changing the maximum
            // here cannot disturb a grade a student has already seen.
            $target = $params['max_grade'] > 0 ? (float) $params['max_grade'] : (float) $quiz->sumgrades;
            if ($target > 0 && abs($target - (float) $quiz->grade) > 0.0001) {
                $calculator->update_quiz_maximum_grade($target);
                $quiz = $DB->get_record('quiz', ['id' => $quiz->id], '*', MUST_EXIST);
            }

            return [
                'quiz_cmid' => (int) $params['quiz_cmid'],
                'added_questions' => array_map(fn($q) => ['id' => (int) $q->id, 'name' => $q->name], $questions),
                'added_random' => $category ? (int) $params['random_count'] : 0,
                'random_category' => $category ? $category->name : null,
                'total_slots' => $DB->count_records('quiz_slots', ['quizid' => $quiz->id]),
                'pages' => (int) $DB->get_field_sql('SELECT MAX(page) FROM {quiz_slots} WHERE quizid = ?', [$quiz->id]),
                'total_marks' => (float) $quiz->sumgrades,
                'max_grade' => (float) $quiz->grade,
                'marks_match_max_grade' => abs((float) $quiz->grade - (float) $quiz->sumgrades) < 0.0001,
            ];
        };
        return dry_run::run($params['dry_run'], (int) $course->id, $write);
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'dry_run' => new external_value(PARAM_BOOL, 'True if nothing was added'),
            'dry_run_note' => dry_run::note_returns(),
            'quiz_cmid' => new external_value(PARAM_INT, 'Quiz course module ID'),
            'added_questions' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Question ID'),
                'name' => new external_value(PARAM_TEXT, 'Question name'),
            ])),
            'added_random' => new external_value(PARAM_INT, 'Random questions added'),
            'random_category' => new external_value(
                PARAM_TEXT,
                'Category they draw from',
                VALUE_REQUIRED,
                null,
                NULL_ALLOWED
            ),
            'total_slots' => new external_value(PARAM_INT, 'Questions in the quiz now'),
            'pages' => new external_value(PARAM_INT, 'Pages in the quiz now'),
            'total_marks' => new external_value(PARAM_FLOAT, 'Sum of the question marks'),
            'max_grade' => new external_value(PARAM_FLOAT, 'Maximum grade the total is scaled to'),
            'marks_match_max_grade' => new external_value(PARAM_BOOL, 'The questions are worth exactly the '
                . 'maximum grade, so Moodle does not scale the marks'),
        ]);
    }
}
