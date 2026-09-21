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

namespace local_nitro\mcp;

use local_nitro\event\tool_called;

/**
 * Tests for tool dispatch: identity, errors, the per-course gate and the audit event.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(dispatcher::class)]
final class dispatcher_test extends \advanced_testcase {
    /**
     * A teacher with local/nitro:use in one course only.
     *
     * @return array{0: \stdClass, 1: \stdClass, 2: \stdClass} teacher, course A (gate), course B (no gate)
     */
    private function teacher_with_gate_in_one_course(): array {
        set_config('active', 1, 'local_nitro');
        $gen = $this->getDataGenerator();
        $a = $gen->create_course(['shortname' => 'A']);
        $b = $gen->create_course(['shortname' => 'B']);
        $teacher = $gen->create_and_enrol($a, 'editingteacher');
        $gen->enrol_user($teacher->id, $b->id, 'editingteacher');
        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $teacher->id, \context_course::instance($a->id));
        return [$teacher, $a, $b];
    }

    public function test_successful_call_runs_as_user(): void {
        $this->resetAfterTest();
        [$teacher, $a] = $this->teacher_with_gate_in_one_course();
        $this->setUser($teacher);

        $result = dispatcher::call('list_courses', registry::function_info('list_courses'), [], 'client1');

        $this->assertFalse($result['isError']);
        $this->assertSame([(int) $a->id], array_column($result['data']['courses'], 'id'));
    }

    public function test_invalid_arguments_name_the_parameter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $result = dispatcher::call('list_courses', registry::function_info('list_courses'), ['bogus' => 1], 'client1');
        $this->assertTrue($result['isError']);
        $this->assertStringContainsString('bogus', $result['message']);
    }

    public function test_gate_granted_in_one_course_only(): void {
        $this->resetAfterTest();
        [$teacher, , $b] = $this->teacher_with_gate_in_one_course();
        $this->setUser($teacher);

        $result = dispatcher::call('list_courses', registry::function_info('list_courses'), ['courseid' => $b->id], 'c');

        $this->assertTrue($result['isError']);
        $this->assertStringContainsString(get_string('nitro:use', 'local_nitro'), $result['message']);
    }

    public function test_every_call_is_logged(): void {
        $this->resetAfterTest();
        [$teacher, , $b] = $this->teacher_with_gate_in_one_course();
        $this->setUser($teacher);
        $sink = $this->redirectEvents();

        dispatcher::call('list_courses', registry::function_info('list_courses'), [], 'client1');
        dispatcher::call('list_courses', registry::function_info('list_courses'), ['courseid' => $b->id], 'client1');

        $events = array_values(array_filter($sink->get_events(), fn($e) => $e instanceof tool_called));
        $this->assertCount(2, $events);
        $this->assertSame('success', $events[0]->other['outcome']);
        $this->assertSame('error', $events[1]->other['outcome']);
        $this->assertEquals($b->id, $events[1]->courseid);
        $this->assertEquals($teacher->id, $events[1]->userid);
        $this->assertSame('client1', $events[1]->other['clientid']);
    }
}
