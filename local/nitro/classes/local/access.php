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

/**
 * Access checks shared by the tools.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /**
     * Checks that the current user may work on a course through nitro: validates the course context
     * (as every external function must) and requires the local/nitro:use gate there.
     *
     * @param int $courseid
     * @return \context_course
     */
    public static function require_course(int $courseid): \context_course {
        // The kill switch holds for every entry point, also if an admin exposes the tools as a web service.
        if (!plugin::active()) {
            throw new \moodle_exception('autherror_disabled', 'local_nitro');
        }
        $context = \context_course::instance($courseid);
        \core_external\external_api::validate_context($context);
        require_capability('local/nitro:use', $context);
        return $context;
    }

    /**
     * Checks a course module the same way, through its course.
     *
     * @param int $cmid
     * @return \context_module
     */
    public static function require_module(int $cmid): \context_module {
        $context = \context_module::instance($cmid);
        self::require_course($context->get_course_context()->instanceid);
        \core_external\external_api::validate_context($context);
        return $context;
    }

    /**
     * Users the current user may see in a course or activity under its group mode, as the web UI would.
     *
     * In separate groups mode, a user without moodle/site:accessallgroups sees only members of their own
     * groups, and may only name one of those groups.
     *
     * @param int $courseid
     * @param \cm_info|null $cm the activity, whose group mode applies; null for the course's
     * @param int $groupid group asked for; 0 for all the user may see
     * @return int[]|null allowed user IDs, or null when there is no restriction beyond $groupid
     */
    public static function visible_users(int $courseid, ?\cm_info $cm, int $groupid): ?array {
        global $USER;
        $course = get_course($courseid);
        $mode = $cm ? groups_get_activity_groupmode($cm, $course) : groups_get_course_groupmode($course);
        $context = $cm ? \context_module::instance($cm->id) : \context_course::instance($courseid);
        if ($mode != SEPARATEGROUPS || has_capability('moodle/site:accessallgroups', $context)) {
            return null;
        }
        $mine = array_keys(groups_get_all_groups($courseid, $USER->id, $cm ? $cm->groupingid : 0));
        if ($groupid && !in_array($groupid, $mine)) {
            throw new \required_capability_exception($context, 'moodle/site:accessallgroups', 'nopermissions', '');
        }
        $ids = [];
        foreach ($groupid ? [$groupid] : $mine as $id) {
            $ids = array_merge($ids, array_keys(groups_get_members($id, 'u.id')));
        }
        return array_values(array_unique(array_map('intval', $ids)));
    }
}
