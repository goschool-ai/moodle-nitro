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
 * Tool list_forum_posts: forum discussions and their posts, as the teacher may see them.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_forum_posts extends external_api {
    /** @var int Longest post text returned, in characters. */
    private const MAX_TEXT = 5000;

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'cmid' => new external_value(PARAM_INT, 'Only this forum, by course module ID (from course_overview); '
                . '0 for every forum of the course, announcements included', VALUE_DEFAULT, 0),
            'discussions' => new external_value(
                PARAM_INT,
                'How many discussions per forum, newest first',
                VALUE_DEFAULT,
                10
            ),
            'posts_per_discussion' => new external_value(PARAM_INT, 'How many posts per discussion, oldest first; '
                . '1 returns only the opening post', VALUE_DEFAULT, 20),
        ]);
    }

    /**
     * Lists discussions with their posts.
     *
     * @param int $courseid
     * @param int $cmid
     * @param int $discussions
     * @param int $postsperdiscussion
     * @return array
     */
    public static function execute(
        int $courseid,
        int $cmid = 0,
        int $discussions = 10,
        int $postsperdiscussion = 20
    ): array {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid, 'cmid' => $cmid, 'discussions' => $discussions,
            'posts_per_discussion' => $postsperdiscussion,
        ]);
        access::require_course($params['courseid']);
        if (
            $params['discussions'] < 1 || $params['discussions'] > 100
                || $params['posts_per_discussion'] < 1 || $params['posts_per_discussion'] > 200
        ) {
            throw new \invalid_parameter_exception('discussions must be 1 to 100 and posts_per_discussion 1 to 200.');
        }

        $modinfo = get_fast_modinfo($params['courseid']);
        $course = $modinfo->get_course();
        $forums = [];
        foreach ($modinfo->get_instances_of('forum') as $cm) {
            if ($params['cmid'] && (int) $cm->id !== $params['cmid']) {
                continue;
            }
            if (!$cm->uservisible || !has_capability('mod/forum:viewdiscussion', \context_module::instance($cm->id))) {
                continue;
            }
            $forums[] = $cm;
        }
        if ($params['cmid'] && !$forums) {
            // Name the forums this course has, so the next call can use the right course module ID.
            $available = [];
            foreach ($modinfo->get_instances_of('forum') as $cm) {
                if ($cm->uservisible && has_capability('mod/forum:viewdiscussion', \context_module::instance($cm->id))) {
                    $available[] = $cm->get_formatted_name() . ' (cmid ' . $cm->id . ')';
                }
            }
            throw new \invalid_parameter_exception("Course module {$params['cmid']} is not a forum you can read in "
                . 'this course. ' . ($available
                    ? 'Forums here: ' . implode(', ', $available) . '. Use one of those course module IDs, or leave '
                        . 'cmid out for all of them.'
                    : 'This course has no forum you can read.'));
        }

        $result = [];
        foreach ($forums as $cm) {
            $context = \context_module::instance($cm->id);
            $forum = $DB->get_record('forum', ['id' => $cm->instance], '*', MUST_EXIST);
            $entries = [];
            // Core's forum_get_discussions() applies the forum's group mode and timed discussions.
            foreach (forum_get_discussions($cm, 'd.timemodified DESC', true, -1, $params['discussions']) as $discussion) {
                $posts = [];
                $records = $DB->get_records(
                    'forum_posts',
                    ['discussion' => $discussion->discussion],
                    'created ASC',
                    '*',
                    0,
                    $params['posts_per_discussion']
                );
                foreach ($records as $post) {
                    if (!forum_user_can_see_post($forum, $discussion, $post, $USER, $cm)) {
                        continue;
                    }
                    $text = format_text($post->message, $post->messageformat, ['context' => $context, 'para' => false]);
                    $text = trim(html_to_text($text, 0, false));
                    $posts[] = [
                        'id' => (int) $post->id,
                        'replyto' => (int) $post->parent,
                        'author' => self::author($post->userid),
                        'subject' => format_string($post->subject, true, ['context' => $context]),
                        'created' => dates::iso((int) $post->created),
                        'text' => \core_text::substr($text, 0, self::MAX_TEXT),
                    ];
                }
                if (!$posts) {
                    continue;
                }
                $entries[] = [
                    'id' => (int) $discussion->discussion,
                    'subject' => format_string($discussion->name, true, ['context' => $context]),
                    'author' => self::author($discussion->userid),
                    'created' => dates::iso((int) $discussion->created),
                    'lastpost' => dates::iso((int) $discussion->timemodified),
                    'pinned' => !empty($discussion->pinned),
                    'url' => (new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->discussion]))->out(false),
                    'posts' => $posts,
                ];
            }
            $result[] = [
                'cmid' => (int) $cm->id,
                'name' => $cm->get_formatted_name(),
                'type' => $forum->type,
                'discussions' => $entries,
            ];
        }
        unset($course);
        return ['forums' => $result];
    }

    /**
     * A user's name for a post; deleted users are named as Moodle does.
     *
     * @param int $userid
     * @return string
     */
    private static function author(int $userid): string {
        $user = \core_user::get_user($userid, '*', IGNORE_MISSING);
        return $user ? fullname($user) : get_string('deleteduser', 'moodle');
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'forums' => new external_multiple_structure(new external_single_structure([
                'cmid' => new external_value(PARAM_INT, 'Course module ID of the forum'),
                'name' => new external_value(PARAM_TEXT, 'Forum name'),
                'type' => new external_value(PARAM_ALPHA, 'Forum type: news are the announcements'),
                'discussions' => new external_multiple_structure(new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Discussion ID'),
                    'subject' => new external_value(PARAM_TEXT, 'Subject'),
                    'author' => new external_value(PARAM_TEXT, 'Who started it'),
                    'created' => new external_value(PARAM_RAW, 'When it started, ISO 8601'),
                    'lastpost' => new external_value(PARAM_RAW, 'Last post, ISO 8601'),
                    'pinned' => new external_value(PARAM_BOOL, 'Pinned to the top'),
                    'url' => new external_value(PARAM_URL, 'Discussion URL'),
                    'posts' => new external_multiple_structure(new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'Post ID'),
                        'replyto' => new external_value(PARAM_INT, 'Post it replies to; 0 for the opening post'),
                        'author' => new external_value(PARAM_TEXT, 'Author'),
                        'subject' => new external_value(PARAM_TEXT, 'Subject'),
                        'created' => new external_value(PARAM_RAW, 'When it was posted, ISO 8601'),
                        'text' => new external_value(PARAM_RAW, 'The message as plain text'),
                    ])),
                ])),
            ])),
        ]);
    }
}
