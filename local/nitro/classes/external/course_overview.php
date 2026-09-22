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
use local_nitro\local\dates;

/**
 * Tool course_overview: sections and activities of a course, as the teacher sees them.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_overview extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
        ]);
    }

    /**
     * Returns the course's sections in order, with their activities, and the number of students.
     *
     * @param int $courseid
     * @return array
     */
    public static function execute(int $courseid): array {
        global $USER, $DB;
        ['courseid' => $courseid] = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);
        $context = access::require_course($courseid);
        // Core's activity dates for an assignment end at the due date; the cut-off is what "can they still
        // submit?" depends on.
        $cutoffs = $DB->get_records_menu('assign', ['course' => $courseid], '', 'id, cutoffdate');

        $modinfo = get_fast_modinfo($courseid);
        $course = $modinfo->get_course();
        $sections = [];
        foreach ($modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible && !$section->visible && !has_capability('moodle/course:viewhiddensections', $context)) {
                continue;
            }
            $activities = [];
            foreach ($modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if (!$cm->uservisible && !has_capability('moodle/course:viewhiddenactivities', $context)) {
                    continue;
                }
                if ($cm->deletioninprogress) {
                    continue;
                }
                $activities[] = [
                    'cmid' => (int) $cm->id,
                    'key' => (string) $cm->idnumber,
                    'type' => $cm->modname,
                    'name' => $cm->get_formatted_name(),
                    'visible' => (bool) $cm->visible,
                    'dates' => self::dates($cm, (int) ($cutoffs[$cm->instance] ?? 0)),
                ];
            }
            $sections[] = [
                'number' => (int) $section->section,
                'name' => get_section_name($course, $section),
                'visible' => (bool) $section->visible,
                'activities' => $activities,
            ];
        }

        return [
            'course' => [
                'id' => (int) $course->id,
                'shortname' => format_string($course->shortname, true, ['context' => $context]),
                'fullname' => format_string($course->fullname, true, ['context' => $context]),
                'visible' => (bool) $course->visible,
            ],
            'students' => count_enrolled_users($context, 'mod/assign:submit', 0, true),
            'sections' => $sections,
        ];
    }

    /**
     * The dates of an activity as the teacher sees them, plus an assignment's cut-off.
     *
     * @param \cm_info $cm
     * @param int $cutoff the assignment's cut-off date, 0 for none or for other activities
     * @return array
     */
    private static function dates(\cm_info $cm, int $cutoff): array {
        global $USER;
        $dates = array_map(fn($date) => [
            'type' => (string) ($date['dataid'] ?? ''),
            'label' => (string) $date['label'],
            'date' => dates::iso((int) $date['timestamp']),
        ], \core\activity_dates::get_dates_for_module($cm, $USER->id));
        if ($cm->modname === 'assign' && $cutoff > 0) {
            $dates[] = ['type' => 'cutoffdate', 'label' => get_string('cutoffdate', 'assign') . ':',
                'date' => dates::iso($cutoff)];
        }
        return $dates;
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'course' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course ID'),
                'shortname' => new external_value(PARAM_TEXT, 'Short name'),
                'fullname' => new external_value(PARAM_TEXT, 'Full name'),
                'visible' => new external_value(PARAM_BOOL, 'False if hidden from students'),
            ]),
            'students' => new external_value(PARAM_INT, 'Number of actively enrolled students'),
            'sections' => new external_multiple_structure(new external_single_structure([
                'number' => new external_value(PARAM_INT, 'Section number (0 is the general section)'),
                'name' => new external_value(PARAM_TEXT, 'Section name'),
                'visible' => new external_value(PARAM_BOOL, 'False if hidden from students'),
                'activities' => new external_multiple_structure(new external_single_structure([
                    'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                    'key' => new external_value(PARAM_RAW, 'Stable key (ID number); empty if none'),
                    'type' => new external_value(PARAM_PLUGIN, 'Activity type, for example assign, page, quiz'),
                    'name' => new external_value(PARAM_TEXT, 'Name'),
                    'visible' => new external_value(PARAM_BOOL, 'False if hidden from students'),
                    'dates' => new external_multiple_structure(new external_single_structure([
                        'type' => new external_value(PARAM_ALPHANUMEXT, 'Date kind, for example duedate, timeclose'),
                        'label' => new external_value(PARAM_TEXT, 'Label as shown in Moodle'),
                        'date' => new external_value(PARAM_RAW, 'ISO 8601'),
                    ])),
                ])),
            ])),
        ]);
    }
}
