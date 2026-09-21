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
use local_nitro\local\dates;

/**
 * Tool list_courses: the courses the user teaches and may work on through nitro.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_courses extends external_api {
    /**
     * Parameters: none.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Lists the courses where the user holds local/nitro:use and can manage or grade content,
     * including courses hidden from students.
     *
     * @return array
     */
    public static function execute(): array {
        global $USER, $CFG, $SITE;
        self::validate_context(\context_system::instance());

        $courses = [];
        foreach (enrol_get_all_users_courses($USER->id, true, ['startdate', 'enddate', 'visible']) as $course) {
            $context = \context_course::instance($course->id);
            if (!has_capability('local/nitro:use', $context)) {
                continue;
            }
            if (!has_any_capability(['moodle/course:manageactivities', 'mod/assign:grade'], $context)) {
                continue;
            }
            $courses[] = [
                'id' => (int) $course->id,
                'shortname' => format_string($course->shortname, true, ['context' => $context]),
                'fullname' => format_string($course->fullname, true, ['context' => $context]),
                'visible' => (bool) $course->visible,
                'startdate' => dates::iso((int) $course->startdate),
                'enddate' => dates::iso((int) $course->enddate),
            ];
        }
        return [
            'site' => ['name' => format_string($SITE->fullname), 'url' => $CFG->wwwroot],
            'courses' => $courses,
        ];
    }

    /**
     * Result: the courses.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'site' => new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'Name of the Moodle site these courses are on'),
                'url' => new external_value(PARAM_URL, 'Address of the Moodle site; tell the teacher which '
                    . 'site you are working on when it is not the one they named'),
            ]),
            'courses' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course ID, used by every other tool'),
                'shortname' => new external_value(PARAM_TEXT, 'Short name'),
                'fullname' => new external_value(PARAM_TEXT, 'Full name'),
                'visible' => new external_value(PARAM_BOOL, 'False if the course is hidden from students'),
                'startdate' => new external_value(PARAM_RAW, 'Start date, ISO 8601', VALUE_REQUIRED, null, NULL_ALLOWED),
                'enddate' => new external_value(PARAM_RAW, 'End date, ISO 8601', VALUE_REQUIRED, null, NULL_ALLOWED),
            ])),
        ]);
    }
}
