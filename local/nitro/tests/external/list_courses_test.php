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

/**
 * Tests for list_courses.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(list_courses::class)]
final class list_courses_test extends \advanced_testcase {
    public function test_teacher_courses_not_student_courses(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $a = $gen->create_course(['shortname' => 'A']);
        $b = $gen->create_course(['shortname' => 'B', 'visible' => 0]);
        $c = $gen->create_course(['shortname' => 'C']);
        $teacher = $gen->create_and_enrol($a, 'editingteacher');
        $gen->enrol_user($teacher->id, $b->id, 'editingteacher');
        $gen->enrol_user($teacher->id, $c->id, 'student');
        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $teacher->id, \context_system::instance());
        $this->setUser($teacher);

        $result = list_courses::clean_returnvalue(list_courses::execute_returns(), list_courses::execute());

        $byname = array_column($result['courses'], null, 'shortname');
        $this->assertSame(['A', 'B'], array_keys($byname));
        $this->assertTrue($byname['A']['visible']);
        $this->assertFalse($byname['B']['visible']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T/', $byname['A']['startdate']);
        // The AI may be connected to more than one Moodle; the result says which one this is.
        global $CFG, $SITE;
        $this->assertSame($CFG->wwwroot, $result['site']['url']);
        $this->assertSame(format_string($SITE->fullname), $result['site']['name']);
    }

    public function test_no_gate_no_courses(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $a = $gen->create_course();
        $teacher = $gen->create_and_enrol($a, 'editingteacher');
        $this->setUser($teacher);
        $this->assertSame([], list_courses::execute()['courses']);
    }
}
