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

namespace local_nitro\event;

/**
 * Tests for the tool_called event.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(tool_called::class)]
final class tool_called_test extends \advanced_testcase {
    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        get_log_manager(true);
    }

    /**
     * Creates a tool_called event in a course.
     *
     * @param \stdClass $course
     * @param array $other
     * @return tool_called
     */
    private function make_event(\stdClass $course, array $other): tool_called {
        return tool_called::create([
            'context' => \context_course::instance($course->id),
            'other' => $other,
        ]);
    }

    public function test_stored_in_log_with_identifiers_only(): void {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        $this->make_event($course, [
            'tool' => 'message_students',
            'clientid' => 'https://claude.ai/oauth/mcp-oauth-client-metadata',
            'outcome' => 'success',
            'objectids' => [11, 12],
        ])->trigger();

        $log = $DB->get_record(
            'logstore_standard_log',
            ['eventname' => '\\local_nitro\\event\\tool_called'],
            '*',
            MUST_EXIST
        );
        $this->assertEquals($teacher->id, $log->userid);
        $this->assertEquals($course->id, $log->courseid);
        $other = json_decode($log->other, true);
        $this->assertSame(['tool', 'clientid', 'outcome', 'objectids'], array_keys($other));
        $this->assertSame([11, 12], $other['objectids']);
    }

    public function test_content_fields_are_rejected(): void {
        $course = $this->getDataGenerator()->create_course();
        $this->expectException(\coding_exception::class);
        $this->make_event($course, [
            'tool' => 'message_students',
            'clientid' => 'abc',
            'outcome' => 'success',
            'message' => 'Dear students, ...',
        ]);
    }

    public function test_unknown_outcome_is_rejected(): void {
        $course = $this->getDataGenerator()->create_course();
        $this->expectException(\coding_exception::class);
        $this->make_event($course, ['tool' => 'list_courses', 'clientid' => 'abc', 'outcome' => 'maybe']);
    }

    public function test_error_outcome_is_logged(): void {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $this->setAdminUser();
        $this->make_event($course, ['tool' => 'save_page', 'clientid' => 'abc', 'outcome' => 'error'])->trigger();
        $this->assertTrue($DB->record_exists(
            'logstore_standard_log',
            ['eventname' => '\\local_nitro\\event\\tool_called', 'courseid' => $course->id]
        ));
    }
}
