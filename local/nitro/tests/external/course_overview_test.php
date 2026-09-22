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
 * Tests for course_overview.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(course_overview::class)]
final class course_overview_test extends tool_testcase {
    public function test_overview_of_a_course(): void {
        $this->setup_course(3, ['numsections' => 2]);
        $gen = $this->getDataGenerator();
        $due = strtotime('2026-10-12 23:59');
        $gen->create_module('page', ['course' => $this->course->id, 'section' => 1, 'name' => 'Week 4',
            'idnumber' => 'week4-page']);
        $assign = $gen->create_module('assign', ['course' => $this->course->id, 'section' => 1, 'name' => 'Homework 2',
            'idnumber' => 'hw2', 'duedate' => $due, 'cutoffdate' => $due + 2 * DAYSECS]);
        $gen->create_module('quiz', ['course' => $this->course->id, 'section' => 2, 'name' => 'Draft quiz', 'visible' => 0]);
        $this->setUser($this->teacher);

        $result = course_overview::clean_returnvalue(
            course_overview::execute_returns(),
            course_overview::execute($this->course->id)
        );

        $this->assertSame(3, $result['students']);
        $this->assertSame([0, 1, 2], array_column($result['sections'], 'number'));
        $week = array_column($result['sections'][1]['activities'], null, 'key');
        $this->assertSame(['week4-page', 'hw2'], array_keys($week));
        $this->assertSame('assign', $week['hw2']['type']);
        $this->assertSame((int) $assign->cmid, $week['hw2']['cmid']);
        $duedates = array_column($week['hw2']['dates'], 'date', 'type');
        $this->assertSame((new \DateTimeImmutable('@' . $due))->setTimezone(\core_date::get_user_timezone_object())
            ->format(DATE_ATOM), $duedates['duedate']);
        // The cut-off decides whether students can still submit; core's activity dates leave it out.
        $this->assertSame((new \DateTimeImmutable('@' . ($due + 2 * DAYSECS)))
            ->setTimezone(\core_date::get_user_timezone_object())->format(DATE_ATOM), $duedates['cutoffdate']);
        $quiz = $result['sections'][2]['activities'][0];
        $this->assertSame('Draft quiz', $quiz['name']);
        $this->assertFalse($quiz['visible']);
    }

    public function test_requires_the_gate(): void {
        $this->setup_course();
        $other = $this->getDataGenerator()->create_and_enrol($this->course, 'editingteacher');
        $this->setUser($other);
        $this->expectException(\required_capability_exception::class);
        course_overview::execute($this->course->id);
    }
}
