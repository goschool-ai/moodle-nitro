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

namespace local_nitro\local;

use core_question\local\bank\question_bank_helper;

/**
 * Question banks and categories in the Moodle 5.x model: a course's shared bank is a qbank
 * activity; a quiz has its own bank in its module context.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank {
    /**
     * Refuses to act on the pre-5.0 question bank.
     *
     * @throws \moodle_exception
     */
    public static function require_moodle_5(): void {
        global $CFG;
        if ((int) $CFG->branch < 500) {
            throw new \moodle_exception('quizneedsmoodle5', 'local_nitro', '', $CFG->release);
        }
    }

    /**
     * The course's shared question bank, created when the course has none.
     *
     * @param int $courseid
     * @return \context_module
     */
    public static function shared_bank(int $courseid): \context_module {
        $cm = question_bank_helper::get_default_open_instance_system_type(get_course($courseid), true);
        return \context_module::instance($cm->id);
    }

    /**
     * The bank to use: a quiz's own bank when a quiz is given, else the course's shared bank.
     *
     * @param int $courseid
     * @param int $quizcmid 0 for the shared bank
     * @return \context_module
     */
    public static function bank(int $courseid, int $quizcmid): \context_module {
        if (!$quizcmid) {
            return self::shared_bank($courseid);
        }
        [$course, $cm] = get_course_and_cm_from_cmid($quizcmid, 'quiz');
        if ((int) $course->id !== $courseid) {
            throw new \invalid_parameter_exception("Quiz {$quizcmid} is not in course {$courseid}.");
        }
        return \context_module::instance($cm->id);
    }

    /**
     * A category of a bank by name, optionally created under the bank's top category.
     *
     * @param \context_module $bank
     * @param string $name
     * @param bool $create
     * @return array{0: \stdClass|null, 1: bool} the category and whether it was created
     */
    public static function category(\context_module $bank, string $name, bool $create): array {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');
        $name = trim($name);
        $category = $DB->get_record_select(
            'question_categories',
            'contextid = ? AND LOWER(name) = LOWER(?) AND parent <> 0',
            [$bank->id, $name],
            '*',
            IGNORE_MULTIPLE
        );
        if ($category || !$create) {
            return [$category ?: null, false];
        }
        $top = question_get_top_category($bank->id, true);
        $id = (new \core_question\category_manager())->add_category("{$top->id},{$bank->id}", $name, '');
        return [$DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST), true];
    }

    /**
     * Number of questions a random slot can draw from a category (latest ready versions).
     *
     * @param int $categoryid
     * @return int
     */
    public static function count_ready(int $categoryid): int {
        global $DB;
        $sql = "SELECT COUNT(DISTINCT qbe.id)
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                  JOIN {question} q ON q.id = qv.questionid
                 WHERE qbe.questioncategoryid = :cat AND qv.status = :ready AND q.parent = 0
                   AND q.qtype <> 'random'";
        $ready = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        return $DB->count_records_sql($sql, ['cat' => $categoryid, 'ready' => $ready]);
    }

    /**
     * Name of a bank for results.
     *
     * @param \context_module $bank
     * @return string
     */
    public static function bank_name(\context_module $bank): string {
        return $bank->get_context_name(false, true);
    }
}
