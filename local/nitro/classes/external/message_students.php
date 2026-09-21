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
use local_nitro\local\confirmation;
use local_nitro\local\content;

/**
 * Tool message_students: one-to-one messages from the teacher, after an approved preview.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_students extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'message' => new external_value(PARAM_RAW, 'The message in markdown'),
            'userids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'User ID'),
                'Recipients by user ID (from list_participants or list_submissions). Give userids, groupid or '
                . 'all_students.',
                VALUE_DEFAULT,
                []
            ),
            'groupid' => new external_value(PARAM_INT, 'All students of this group', VALUE_DEFAULT, 0),
            'all_students' => new external_value(PARAM_BOOL, 'All students of the course', VALUE_DEFAULT, false),
            'confirmation_token' => confirmation::param(),
        ]);
    }

    /**
     * Previews or sends the message.
     *
     * @param int $courseid
     * @param string $message
     * @param array $userids
     * @param int $groupid
     * @param bool $allstudents
     * @param string $confirmationtoken
     * @return array
     */
    public static function execute(
        int $courseid,
        string $message,
        array $userids = [],
        int $groupid = 0,
        bool $allstudents = false,
        string $confirmationtoken = ''
    ): array {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/message/lib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'message' => $message, 'userids' => $userids, 'groupid' => $groupid,
            'all_students' => $allstudents, 'confirmation_token' => $confirmationtoken,
        ]);
        $context = access::require_course($params['courseid']);
        require_capability('moodle/site:sendmessage', $context);
        require_capability('moodle/course:viewparticipants', $context);
        if (trim($params['message']) === '') {
            throw new \invalid_parameter_exception('message must not be empty.');
        }
        $selectors = (int) (bool) $params['userids'] + (int) (bool) $params['groupid'] + (int) $params['all_students'];
        if ($selectors !== 1) {
            throw new \invalid_parameter_exception('Give exactly one of userids, groupid or all_students.');
        }

        $recipients = self::recipients($context, $params);
        $visible = access::visible_users($params['courseid'], null, $params['groupid']);
        foreach ($recipients as $user) {
            if ($visible !== null && !in_array((int) $user->id, $visible, true)) {
                throw new \invalid_parameter_exception("User {$user->id} is not in one of your groups. Nothing was sent.");
            }
        }
        // The confirmation covers exactly the recipients the teacher saw, even if enrolments change meanwhile.
        $pinned = $params + ['_recipients' => array_map(fn($u) => (int) $u->id, $recipients)];
        $converted = content::from_markdown($params['message']);
        $entries = [];
        foreach ($recipients as $user) {
            $reason = self::undeliverable($user);
            $entries[] = ['id' => (int) $user->id, 'fullname' => fullname($user), 'reason' => $reason];
        }

        $base = [
            'recipients' => count($entries),
            'message_html' => $converted['html'],
            'message_text' => content::to_plain($params['message']),
            'removed_by_cleaning' => $converted['removed'],
        ];
        if ($params['confirmation_token'] === '') {
            $issued = confirmation::issue('message_students', $pinned);
            return $base + confirmation::result_fields($issued) + [
                'delivered' => [],
                'not_delivered' => array_values(array_filter($entries, fn($e) => $e['reason'] !== '')),
                'preview_recipients' => $entries,
                'affected_ids' => [],
            ];
        }

        confirmation::redeem('message_students', $pinned);
        $delivered = [];
        $failed = [];
        foreach ($recipients as $user) {
            $reason = self::undeliverable($user);
            if ($reason === '' && message_post_message($USER, $user, $converted['html'], FORMAT_HTML)) {
                $delivered[] = ['id' => (int) $user->id, 'fullname' => fullname($user), 'reason' => ''];
            } else {
                $failed[] = ['id' => (int) $user->id, 'fullname' => fullname($user),
                    'reason' => $reason ?: 'Moodle did not accept the message.'];
            }
        }
        return $base + confirmation::result_fields(null) + [
            'delivered' => $delivered,
            'not_delivered' => $failed,
            'preview_recipients' => [],
            'affected_ids' => array_column($delivered, 'id'),
        ];
    }

    /**
     * The recipients the call selects; every one must be a student of the course.
     *
     * @param \context_course $context
     * @param array $params
     * @return \stdClass[]
     */
    private static function recipients(\context_course $context, array $params): array {
        global $DB;
        $students = [];
        foreach (get_archetype_roles('student') as $role) {
            foreach (get_role_users($role->id, $context, false, 'u.*', 'u.lastname, u.firstname') as $user) {
                $students[$user->id] = $user;
            }
        }
        if ($params['userids']) {
            $selected = [];
            foreach (array_unique($params['userids']) as $id) {
                if (!isset($students[$id]) || !is_enrolled($context, $id, '', true)) {
                    throw new \invalid_parameter_exception("User {$id} is not an active student of this course. "
                        . 'Nothing was sent.');
                }
                $selected[$id] = $students[$id];
            }
            return array_values($selected);
        }
        if ($params['groupid']) {
            if (!$DB->record_exists('groups', ['id' => $params['groupid'], 'courseid' => $context->instanceid])) {
                throw new \invalid_parameter_exception("Group {$params['groupid']} does not belong to this course.");
            }
            $members = groups_get_members($params['groupid'], 'u.id');
            $students = array_intersect_key($students, $members);
        }
        $active = array_filter($students, fn($user) => is_enrolled($context, $user->id, '', true));
        if (!$active) {
            throw new \invalid_parameter_exception('The selection contains no active students. Nothing was sent.');
        }
        return array_values($active);
    }

    /**
     * Why a message cannot reach a user, or '' if it can.
     *
     * @param \stdClass $user
     * @return string
     */
    private static function undeliverable(\stdClass $user): string {
        global $USER;
        if ($user->suspended) {
            return 'The account is suspended.';
        }
        if (!\core_message\api::can_send_message($user->id, $USER->id)) {
            return 'Their messaging settings do not accept messages from you (or they blocked you).';
        }
        return '';
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $person = fn($desc) => new external_multiple_structure(new external_single_structure([
            'id' => new external_value(PARAM_INT, 'User ID'),
            'fullname' => new external_value(PARAM_TEXT, 'Name'),
            'reason' => new external_value(PARAM_TEXT, 'Why it cannot be delivered; empty if it can'),
        ]), $desc);
        return new external_single_structure(confirmation::result_returns() + [
            'recipients' => new external_value(PARAM_INT, 'Number of recipients'),
            'message_html' => new external_value(PARAM_RAW, 'The message as it will be sent'),
            'message_text' => new external_value(PARAM_RAW, 'The message as plain text, to show the teacher'),
            'removed_by_cleaning' => new external_multiple_structure(new external_value(PARAM_RAW, 'Removed item')),
            'preview_recipients' => $person('Everyone who would receive it (preview only)'),
            'delivered' => $person('Who received it'),
            'not_delivered' => $person('Who cannot or did not receive it, with the reason'),
            'affected_ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'User ID'),
                'Recipients, for the log'
            ),
        ]);
    }
}
