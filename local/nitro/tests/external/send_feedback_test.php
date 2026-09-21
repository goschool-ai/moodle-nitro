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
 * Tests for send_feedback.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(send_feedback::class)]
final class send_feedback_test extends tool_testcase {
    /**
     * Calls the tool.
     *
     * @param string $message
     * @param string $tool
     * @param string $token
     * @return array
     */
    private function call(string $message, string $tool = '', string $token = ''): array {
        return send_feedback::clean_returnvalue(
            send_feedback::execute_returns(),
            send_feedback::execute($message, $tool, $token)
        );
    }

    public function test_preview_then_send(): void {
        $this->setup_course(0);
        set_config('feedbackemail', 'nitro@example.com', 'local_nitro');
        $this->setUser($this->teacher);
        $sink = $this->redirectEmails();

        $preview = $this->call('Nem tudok fájlt feltölteni a feladathoz.', 'save_assignment');

        $this->assertFalse($preview['executed']);
        $this->assertFalse($preview['sent']);
        $this->assertSame('nitro@example.com', $preview['recipient']);
        $this->assertStringContainsString('Nem tudok fájlt feltölteni', $preview['message']);
        $this->assertStringContainsString('nitro: ', $preview['message']);
        $this->assertStringContainsString('moodle: ', $preview['message']);
        $this->assertStringContainsString('tool: save_assignment', $preview['message']);
        $this->assertCount(0, $sink->get_messages());

        $done = $this->call(
            'Nem tudok fájlt feltölteni a feladathoz.',
            'save_assignment',
            $preview['confirmation_token']
        );

        $this->assertTrue($done['executed']);
        $this->assertTrue($done['sent']);
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $this->assertSame('nitro@example.com', $messages[0]->to);
        $this->assertStringContainsString('nitro feedback: save_assignment', $messages[0]->subject);
        $this->assertStringNotContainsString($this->teacher->email, $messages[0]->body);
    }

    public function test_changed_text_needs_a_new_preview(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $sink = $this->redirectEmails();
        $preview = $this->call('Első szöveg.');
        try {
            $this->call('Átírt szöveg.', '', $preview['confirmation_token']);
            $this->fail('Expected a refusal');
        } catch (\moodle_exception $e) {
            $this->assertSame('confirmchanged', $e->errorcode);
        }
        $this->assertCount(0, $sink->get_messages());
    }

    public function test_switched_off(): void {
        $this->setup_course(0);
        set_config('feedbackenabled', 0, 'local_nitro');
        $this->setUser($this->teacher);
        $this->assertNotContains('send_feedback', \local_nitro\local\tools::allowed());
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessageMatches('/switched off/');
        $this->call('Valami.');
    }

    public function test_user_without_the_gate(): void {
        $this->setup_course(0);
        $outsider = $this->getDataGenerator()->create_user();
        $this->setUser($outsider);
        $this->expectException(\required_capability_exception::class);
        $this->call('Valami.');
    }

    public function test_unreachable_mail_server_queues_a_retry(): void {
        $this->setup_course(0);
        set_config('feedbackemail', 'nitro@example.com', 'local_nitro');
        $this->setUser($this->teacher);
        $sink = $this->redirectEmails();
        $preview = $this->call('SMTP-hiba miatt nem ment ki.');

        \local_nitro\local\mailer::fail_next_send_for_testing();
        $done = $this->call('SMTP-hiba miatt nem ment ki.', '', $preview['confirmation_token']);

        // The teacher's report is accepted and waiting, not silently dropped.
        $this->assertTrue($done['executed']);
        $this->assertFalse($done['sent']);
        $this->assertTrue($done['queued']);
        $this->assertNotEmpty($done['note']);
        $this->assertCount(0, $sink->get_messages());

        ob_start();
        $this->runAdhocTasks(\local_nitro\task\send_feedback_mail::class);
        ob_end_clean();

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $this->assertSame('nitro@example.com', $messages[0]->to);
        $this->assertStringContainsString('nitro feedback', $messages[0]->subject);
        $this->assertStringContainsString('SMTP-hiba miatt nem ment ki.', $messages[0]->body);
    }

    public function test_no_address_configured(): void {
        $this->setup_course(0);
        set_config('feedbackemail', '', 'local_nitro');
        $this->setUser($this->teacher);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessageMatches('/valid feedback address/');
        $this->call('Valami.');
    }

    public function test_empty_message(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->call('   ');
    }
}
