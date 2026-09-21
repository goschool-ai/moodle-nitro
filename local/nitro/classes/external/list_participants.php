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
 * Tool list_participants: enrolled users with roles, groups and last access to the course.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_participants extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'role' => new external_value(PARAM_ALPHANUMEXT, 'Only users with this role, by short name '
                . '(student, teacher, editingteacher); empty for everyone', VALUE_DEFAULT, ''),
            'groupid' => new external_value(PARAM_INT, 'Only members of this group; 0 for everyone', VALUE_DEFAULT, 0),
            'never_accessed' => new external_value(
                PARAM_BOOL,
                'Only users who never opened the course',
                VALUE_DEFAULT,
                false
            ),
            'include_identity' => new external_value(PARAM_BOOL, 'Also return email address and ID number, where the '
                . 'teacher may see them in Moodle. Only ask for these when the task needs them.', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Lists participants.
     *
     * @param int $courseid
     * @param string $role
     * @param int $groupid
     * @param bool $neveraccessed
     * @param bool $includeidentity
     * @return array
     */
    public static function execute(
        int $courseid,
        string $role = '',
        int $groupid = 0,
        bool $neveraccessed = false,
        bool $includeidentity = false
    ): array {
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'role' => $role, 'groupid' => $groupid,
            'never_accessed' => $neveraccessed, 'include_identity' => $includeidentity,
        ]);
        $context = access::require_course($params['courseid']);
        require_capability('moodle/course:viewparticipants', $context);

        $roleid = 0;
        if ($params['role'] !== '') {
            $roleid = (int) $DB->get_field('role', 'id', ['shortname' => $params['role']]);
            if (!$roleid) {
                throw new \invalid_parameter_exception("Unknown role '{$params['role']}'. Use student, teacher or editingteacher.");
            }
        }
        if (
            $params['groupid'] && !$DB->record_exists('groups', ['id' => $params['groupid'],
                'courseid' => $params['courseid']])
        ) {
            throw new \invalid_parameter_exception("Group {$params['groupid']} does not belong to this course.");
        }
        $visible = access::visible_users($params['courseid'], null, $params['groupid']);

        $identity = [];
        if ($params['include_identity']) {
            $identity = array_values(array_intersect(
                ['email', 'idnumber'],
                \core_user\fields::get_identity_fields($context, false)
            ));
        }

        $users = get_enrolled_users($context, '', $params['groupid'], 'u.*', 'u.lastname, u.firstname', 0, 0, true);
        $lastaccess = $DB->get_records_menu('user_lastaccess', ['courseid' => $params['courseid']], '', 'userid, timeaccess');
        $groupnames = [];
        foreach (groups_get_all_groups($params['courseid']) as $group) {
            $groupnames[$group->id] = format_string($group->name, true, ['context' => $context]);
        }

        $result = [];
        foreach ($users as $user) {
            if ($visible !== null && !in_array((int) $user->id, $visible, true)) {
                continue;
            }
            $roles = get_user_roles($context, $user->id, true);
            if ($roleid && !in_array($roleid, array_column($roles, 'roleid'))) {
                continue;
            }
            $accessed = $lastaccess[$user->id] ?? null;
            if ($params['never_accessed'] && $accessed) {
                continue;
            }
            // Group IDs of the user in this course, across groupings (the values, not the keys).
            $ids = groups_get_user_groups($params['courseid'], $user->id)[0] ?? [];
            $entry = [
                'id' => (int) $user->id,
                'fullname' => fullname($user),
                'roles' => array_values(array_unique(array_column($roles, 'shortname'))),
                'groups' => array_values(array_intersect_key($groupnames, array_flip($ids))),
                'lastaccess' => dates::iso($accessed ? (int) $accessed : null),
            ];
            foreach ($identity as $field) {
                $entry[$field] = (string) $user->$field;
            }
            $result[] = $entry;
        }
        return ['participants' => $result];
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'participants' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'User ID, used by message_students and grade_submission'),
                'fullname' => new external_value(PARAM_TEXT, 'Full name'),
                'roles' => new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT, 'Role short name')),
                'groups' => new external_multiple_structure(new external_value(PARAM_TEXT, 'Group name')),
                'lastaccess' => new external_value(
                    PARAM_RAW,
                    'Last access to this course, ISO 8601; null if never',
                    VALUE_REQUIRED,
                    null,
                    NULL_ALLOWED
                ),
                'email' => new external_value(PARAM_RAW, 'Email address, only when include_identity was set', VALUE_OPTIONAL),
                'idnumber' => new external_value(PARAM_RAW, 'ID number, only when include_identity was set', VALUE_OPTIONAL),
            ])),
        ]);
    }
}
