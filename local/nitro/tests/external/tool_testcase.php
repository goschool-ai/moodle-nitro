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
 * Shared set-up for the tool tests: a course, a teacher with the nitro gate, students.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tool_testcase extends \advanced_testcase {
    /** @var \stdClass The course. */
    protected \stdClass $course;

    /** @var \stdClass Editing teacher holding local/nitro:use in the course. */
    protected \stdClass $teacher;

    /** @var \stdClass[] Students. */
    protected array $students = [];

    /** @var int Role that grants local/nitro:use. */
    protected int $gaterole;

    /**
     * Creates the course, the teacher with the gate, and students.
     *
     * @param int $students
     * @param array $courserecord
     */
    protected function setup_course(int $students = 3, array $courserecord = []): void {
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $gen = $this->getDataGenerator();
        $this->course = $gen->create_course($courserecord);
        $this->teacher = $gen->create_and_enrol($this->course, 'editingteacher', ['firstname' => 'Tanár', 'lastname' => 'Anna']);
        $this->gaterole = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $this->gaterole, \context_system::instance());
        $this->grant_gate($this->teacher);
        for ($i = 1; $i <= $students; $i++) {
            $this->students[] = $gen->create_and_enrol(
                $this->course,
                'student',
                ['firstname' => 'Diák', 'lastname' => (string) $i, 'email' => "diak{$i}@example.com", 'idnumber' => "N{$i}"]
            );
        }
    }

    /**
     * Grants local/nitro:use in the course.
     *
     * @param \stdClass $user
     */
    protected function grant_gate(\stdClass $user): void {
        role_assign($this->gaterole, $user->id, \context_course::instance($this->course->id));
    }
}
