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
 * Tool post_announcement: a discussion in the course's announcements forum, after an approved preview.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_announcement extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'subject' => new external_value(PARAM_TEXT, 'Subject'),
            'message' => new external_value(PARAM_RAW, 'The announcement in markdown'),
            'confirmation_token' => confirmation::param(),
        ]);
    }

    /**
     * Previews or posts the announcement.
     *
     * @param int $courseid
     * @param string $subject
     * @param string $message
     * @param string $confirmationtoken
     * @return array
     */
    public static function execute(int $courseid, string $subject, string $message, string $confirmationtoken = ''): array {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'subject' => $subject, 'message' => $message, 'confirmation_token' => $confirmationtoken,
        ]);
        $context = access::require_course($params['courseid']);
        if (trim($params['subject']) === '' || trim($params['message']) === '') {
            throw new \invalid_parameter_exception('subject and message must not be empty.');
        }
        $course = get_course($params['courseid']);
        $forum = $DB->get_record('forum', ['course' => $course->id, 'type' => 'news'], '*', IGNORE_MULTIPLE);
        $forumcontext = $forum ? \context_module::instance(get_coursemodule_from_instance('forum', $forum->id)->id) : $context;
        require_capability('mod/forum:addnews', $forumcontext);

        $converted = content::from_markdown($params['message']);
        $warnings = [];
        if (!$course->visible) {
            $warnings[] = 'The course is hidden from students: they cannot open it, so they cannot read the announcement. '
                . 'Make the course visible first if they should.';
        }
        if (!$forum) {
            $warnings[] = 'The course has no announcements forum yet; it will be created.';
        }
        $readers = count_enrolled_users($context, 'mod/forum:viewdiscussion', 0, true);
        $notified = $forum
            ? count(\mod_forum\subscriptions::fetch_subscribed_users($forum, 0, $forumcontext, 'u.id'))
            : self::mailable($context);
        if ($notified < $readers) {
            $warnings[] = "Moodle will email {$notified} of the {$readers} people who can read the course. Accounts "
                . 'that cannot sign in, are suspended or were never confirmed get no mail; on a practice site the '
                . 'fictitious students are such accounts. Everyone still sees the announcement in the course.';
        }
        $base = [
            'subject' => $params['subject'],
            'message_html' => $converted['html'],
            'message_text' => content::to_plain($params['message']),
            'removed_by_cleaning' => $converted['removed'],
            'notified_users' => $notified,
            'when_notified' => 'Moodle emails subscribers after the ' . format_time((int) ($CFG->maxeditingtime ?? 1800))
                . ' editing time, on its next scheduled run.',
            'warnings' => $warnings,
        ];

        if ($params['confirmation_token'] === '') {
            return $base + confirmation::result_fields(confirmation::issue('post_announcement', $params))
                + ['discussionid' => 0, 'url' => '', 'affected_ids' => []];
        }

        confirmation::redeem('post_announcement', $params);
        $forum = forum_get_course_forum($course->id, 'news');
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
        require_capability('mod/forum:addnews', \context_module::instance($cm->id));
        $discussion = (object) [
            'course' => $course->id,
            'forum' => $forum->id,
            'name' => $params['subject'],
            'message' => $converted['html'],
            'messageformat' => FORMAT_HTML,
            'messagetrust' => 0,
            'mailnow' => 0,
            'groupid' => -1,
            'timestart' => 0,
            'timeend' => 0,
            'pinned' => 0,
            'attachments' => null,
        ];
        $discussionid = forum_add_discussion($discussion, null, null, $USER->id);
        $post = $DB->get_record('forum_posts', ['discussion' => $discussionid, 'parent' => 0], '*', MUST_EXIST);
        \mod_forum\event\discussion_created::create([
            'context' => \context_module::instance($cm->id),
            'objectid' => $discussionid,
            'other' => ['forumid' => $forum->id],
        ])->trigger();
        $base['notified_users'] = count(\mod_forum\subscriptions::fetch_subscribed_users(
            $forum,
            0,
            \context_module::instance($cm->id),
            'u.id'
        ));
        return $base + confirmation::result_fields(null) + [
            'discussionid' => (int) $discussionid,
            'url' => (new \moodle_url('/mod/forum/discuss.php', ['d' => $discussionid]))->out(false),
            'affected_ids' => [(int) $post->id],
        ];
    }

    /**
     * How many people the announcements forum would mail once it exists.
     *
     * Mirrors what \mod_forum\subscriptions::get_potential_subscribers() counts, so the preview does not
     * promise more mail than the first real announcement delivers.
     *
     * @param \context_course $context
     * @return int
     */
    private static function mailable(\context_course $context): int {
        global $DB;
        [$esql, $params] = get_enrolled_sql($context, 'mod/forum:allowforcesubscribe', 0, true);
        return $DB->count_records_sql(
            "SELECT COUNT(u.id)
               FROM {user} u
               JOIN ({$esql}) je ON je.id = u.id
              WHERE u.deleted = 0 AND u.auth <> 'nologin' AND u.suspended = 0 AND u.confirmed = 1",
            $params
        );
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(confirmation::result_returns() + [
            'subject' => new external_value(PARAM_TEXT, 'Subject'),
            'message_html' => new external_value(PARAM_RAW, 'The announcement as it will be posted'),
            'message_text' => new external_value(PARAM_RAW, 'The announcement as plain text, to show the teacher'),
            'removed_by_cleaning' => new external_multiple_structure(new external_value(PARAM_RAW, 'Removed item')),
            'notified_users' => new external_value(PARAM_INT, 'Users who are subscribed and will be notified'),
            'when_notified' => new external_value(PARAM_TEXT, 'When notifications go out'),
            'warnings' => new external_multiple_structure(new external_value(PARAM_TEXT, 'Warning to tell the teacher')),
            'discussionid' => new external_value(PARAM_INT, 'The new discussion; 0 in a preview'),
            'url' => new external_value(PARAM_RAW, 'Its URL; empty in a preview'),
            'affected_ids' => new external_multiple_structure(new external_value(PARAM_INT, 'Post ID'), 'For the log'),
        ]);
    }
}
