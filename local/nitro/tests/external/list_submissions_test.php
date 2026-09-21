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
 * Tests for list_submissions.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(list_submissions::class)]
final class list_submissions_test extends tool_testcase {
    /** @var \stdClass The assignment module. */
    private \stdClass $assign;

    /**
     * Course with 4 students and an assignment due yesterday:
     * student 1 submitted on time and is graded, 2 submitted late, 3 has an extension and submitted, 4 missing.
     */
    private function setup_assignment(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->setup_course(4);
        $this->setAdminUser();
        $gen = $this->getDataGenerator();
        $due = time() - DAYSECS;
        $this->assign = $gen->create_module('assign', ['course' => $this->course->id, 'name' => 'Homework 2',
            'duedate' => $due, 'submissiondrafts' => 0, 'assignsubmission_onlinetext_enabled' => 1, 'grade' => 10]);
        $assigngen = $gen->get_plugin_generator('mod_assign');
        $assigngen->create_extension(['cmid' => $this->assign->cmid, 'userid' => $this->students[2]->id,
            'extensionduedate' => time() + DAYSECS]);
        foreach ([0, 1, 2] as $i) {
            $assigngen->create_submission(['userid' => $this->students[$i]->id, 'cmid' => $this->assign->cmid,
                'onlinetext' => '<p>Kész: <a href="https://github.com/diak/hw2">repo</a>, lásd https://gitlab.example/x.</p>']);
        }
        // Student 1 submitted before the due date.
        $DB->set_field(
            'assign_submission',
            'timemodified',
            $due - HOURSECS,
            ['assignment' => $this->assign->id, 'userid' => $this->students[0]->id]
        );
        [$course, $cm] = get_course_and_cm_from_cmid($this->assign->cmid, 'assign');
        $assign = new \assign(\context_module::instance($cm->id), $cm, $course);
        $grade = $assign->get_user_grade($this->students[0]->id, true);
        $grade->grade = 8;
        $assign->update_grade($grade);
    }

    /**
     * Calls the tool.
     *
     * @param string $filter
     * @param bool $content
     * @return array
     */
    private function call(string $filter = 'all', bool $content = false): array {
        return list_submissions::clean_returnvalue(
            list_submissions::execute_returns(),
            list_submissions::execute($this->assign->cmid, $filter, $content, 0)
        );
    }

    /**
     * User IDs in a result.
     *
     * @param array $result
     * @return int[]
     */
    private static function ids(array $result): array {
        return array_column($result['students'], 'userid');
    }

    public function test_all_students_with_state(): void {
        $this->setup_assignment();
        $this->setUser($this->teacher);
        $result = $this->call();

        $this->assertSame(['students' => 4, 'submitted' => 3, 'missing' => 1, 'late' => 1, 'graded' => 1], $result['counts']);
        $byuser = array_column($result['students'], null, 'userid');
        $graded = $byuser[$this->students[0]->id];
        $this->assertSame('submitted', $graded['status']);
        $this->assertTrue($graded['graded']);
        $this->assertStringContainsString('8', $graded['grade']);
        $this->assertFalse($graded['late']);
        $this->assertTrue($byuser[$this->students[1]->id]['late']);
        $this->assertFalse($byuser[$this->students[2]->id]['late']);
        $this->assertNotNull($byuser[$this->students[2]->id]['extension']);
        $this->assertSame('notsubmitted', $byuser[$this->students[3]->id]['status']);
        $this->assertTrue($byuser[$this->students[3]->id]['overdue'], 'nothing submitted after the deadline');
        $this->assertFalse($graded['overdue']);
        $this->assertArrayNotHasKey('onlinetext', $graded);
    }

    public function test_filters(): void {
        $this->setup_assignment();
        $this->setUser($this->teacher);
        $s = array_column($this->students, 'id');
        $this->assertEquals([$s[3]], self::ids($this->call('missing')));
        $this->assertEquals([$s[1]], self::ids($this->call('late')));
        $this->assertEqualsCanonicalizing([$s[1], $s[2]], self::ids($this->call('ungraded')));
        $this->assertEquals([$s[0]], self::ids($this->call('graded')));
    }

    public function test_repository_links(): void {
        $this->setup_assignment();
        $this->setUser($this->teacher);
        $entry = $this->call('late', true)['students'][0];
        $this->assertStringContainsString('Kész', $entry['onlinetext']);
        $this->assertSame(['https://github.com/diak/hw2', 'https://gitlab.example/x'], $entry['links']);
        $this->assertSame([], $entry['files']);
    }

    public function test_student_gets_no_data(): void {
        $this->setup_assignment();
        $this->grant_gate($this->students[0]);
        $this->setUser($this->students[0]);
        $this->expectException(\required_capability_exception::class);
        $this->call();
    }

    public function test_unknown_filter(): void {
        $this->setup_assignment();
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->call('someday');
    }
}
