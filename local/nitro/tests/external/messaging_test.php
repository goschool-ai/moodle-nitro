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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/tool_testcase.php');

/**
 * Tests for message_students and post_announcement.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(message_students::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(post_announcement::class)]
final class messaging_test extends tool_testcase {
    /**
     * Calls message_students.
     *
     * @param array $args
     * @return array
     */
    private function message(array $args): array {
        $args = array_merge(['courseid' => $this->course->id, 'message' => 'Kérlek, add be a **2. házit** péntekig!',
            'userids' => [], 'groupid' => 0, 'all_students' => false, 'confirmation_token' => ''], $args);
        return message_students::clean_returnvalue(
            message_students::execute_returns(),
            message_students::execute(...array_values($args))
        );
    }

    public function test_reminder_to_three_students(): void {
        $this->setup_course(4);
        $this->setUser($this->teacher);
        $ids = array_column(array_slice($this->students, 0, 3), 'id');
        $sink = $this->redirectMessages();

        $preview = $this->message(['userids' => $ids]);
        $this->assertFalse($preview['executed']);
        $this->assertNotEmpty($preview['confirmation_token']);
        $this->assertSame(3, $preview['recipients']);
        $this->assertStringContainsString('<strong>2. házit</strong>', $preview['message_html']);
        $this->assertStringContainsString('2. házit', $preview['message_text']);
        $this->assertStringNotContainsString('2. HÁZIT', $preview['message_text']);
        $this->assertStringNotContainsString('**', $preview['message_text']);
        $this->assertCount(0, $sink->get_messages());

        $done = $this->message(['userids' => $ids, 'confirmation_token' => $preview['confirmation_token']]);
        $this->assertTrue($done['executed']);
        $this->assertCount(3, $done['delivered']);
        $this->assertSame([], $done['not_delivered']);
        $messages = $sink->get_messages();
        $this->assertCount(3, $messages);
        $this->assertEqualsCanonicalizing($ids, array_column($messages, 'useridto'));
        foreach ($messages as $message) {
            $this->assertEquals($this->teacher->id, $message->useridfrom);
        }
    }

    public function test_recipient_not_in_course(): void {
        $this->setup_course(1);
        $outsider = $this->getDataGenerator()->create_user();
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches("/User {$outsider->id} is not an active student/");
        $this->message(['userids' => [$this->students[0]->id, $outsider->id]]);
    }

    public function test_student_does_not_accept_messages(): void {
        global $DB;
        $this->setup_course(3);
        // A teacher who cannot message everyone, and a student who accepts messages from contacts only.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        assign_capability('moodle/site:messageanyuser', CAP_PROHIBIT, $roleid, \context_system::instance(), true);
        accesslib_clear_all_caches_for_unit_testing();
        set_user_preference(
            'message_blocknoncontacts',
            \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS,
            $this->students[1]->id
        );
        $DB->set_field('user', 'suspended', 1, ['id' => $this->students[2]->id]);
        $this->setUser($this->teacher);
        $sink = $this->redirectMessages();
        $ids = array_column($this->students, 'id');

        $preview = $this->message(['userids' => $ids]);
        $this->assertCount(2, $preview['not_delivered']);
        $done = $this->message(['userids' => $ids, 'confirmation_token' => $preview['confirmation_token']]);

        $this->assertSame([(int) $this->students[0]->id], array_column($done['delivered'], 'id'));
        $notdelivered = array_column($done['not_delivered'], 'reason', 'id');
        $this->assertStringContainsString('messaging settings', $notdelivered[$this->students[1]->id]);
        $this->assertStringContainsString('suspended', $notdelivered[$this->students[2]->id]);
        $this->assertCount(1, $sink->get_messages());
    }

    public function test_changed_message_needs_new_preview(): void {
        $this->setup_course(1);
        $this->setUser($this->teacher);
        $sink = $this->redirectMessages();
        $preview = $this->message(['all_students' => true]);
        try {
            $this->message(['all_students' => true, 'message' => 'Más szöveg',
                'confirmation_token' => $preview['confirmation_token']]);
            $this->fail('Expected a refusal');
        } catch (\moodle_exception $e) {
            $this->assertSame('confirmchanged', $e->errorcode);
        }
        $this->assertCount(0, $sink->get_messages());
    }

    /**
     * Calls post_announcement.
     *
     * @param string $token
     * @return array
     */
    private function announce(string $token = ''): array {
        return post_announcement::clean_returnvalue(
            post_announcement::execute_returns(),
            post_announcement::execute($this->course->id, 'Hétfő', 'Hozzátok a laptopot hétfőn!', $token)
        );
    }

    public function test_announcement(): void {
        global $DB;
        $this->setup_course(3);
        $this->setUser($this->teacher);

        $preview = $this->announce();
        $this->assertFalse($preview['executed']);
        $this->assertSame(0, $DB->count_records('forum_discussions', ['course' => $this->course->id]));

        $done = $this->announce($preview['confirmation_token']);
        $this->assertTrue($done['executed']);
        $this->assertGreaterThan(0, $done['discussionid']);
        $this->assertGreaterThanOrEqual(3, $done['notified_users']);
        $forum = $DB->get_record('forum', ['course' => $this->course->id, 'type' => 'news'], '*', MUST_EXIST);
        $discussion = $DB->get_record('forum_discussions', ['id' => $done['discussionid']], '*', MUST_EXIST);
        $this->assertEquals($forum->id, $discussion->forum);
        $this->assertSame('Hétfő', $discussion->name);
        $this->assertStringContainsString('laptopot', $DB->get_field('forum_posts', 'message', ['id' => $discussion->firstpost]));
    }

    public function test_plain_text_keeps_the_lines_and_does_not_shout(): void {
        $this->setup_course(1);
        $this->setUser($this->teacher);
        $this->redirectMessages();
        $preview = $this->message([
            'userids' => [$this->students[0]->id],
            'message' => "A **hatarido** pentek.\n\nUdv,\nAnna",
        ]);
        // Moodle's html_to_text() would send "A HATARIDO PENTEK." with the signature on one line.
        $this->assertSame("A hatarido pentek.\n\nUdv,\nAnna", $preview['message_text']);
    }

    public function test_announcement_says_how_many_get_mail(): void {
        global $DB;
        $this->setup_course(3);
        // Accounts that cannot sign in get no mail; without a word the teacher reads the low number as a fault.
        $DB->set_field('user', 'auth', 'nologin', ['id' => $this->students[0]->id]);
        $this->setUser($this->teacher);

        $preview = $this->announce();

        $warnings = implode(' ', $preview['warnings']);
        $this->assertStringContainsString('cannot sign in', $warnings);
        $this->assertStringContainsString('sees the announcement in the course', $warnings);
    }

    public function test_hidden_course_warning(): void {
        $this->setup_course(1, ['visible' => 0]);
        $this->setUser($this->teacher);
        $preview = $this->announce();
        $this->assertStringContainsString('course is hidden', implode(' ', $preview['warnings']));
    }
}
