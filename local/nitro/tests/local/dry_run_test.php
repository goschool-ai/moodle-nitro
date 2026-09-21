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

namespace local_nitro\local;

/**
 * Tests for dry runs.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(dry_run::class)]
final class dry_run_test extends \advanced_testcase {
    public function test_dry_run_changes_nothing_but_reports_effective_settings(): void {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $before = $DB->count_records('course_modules', ['course' => $course->id]);
        $sink = $this->redirectEvents();

        $result = dry_run::run(true, $course->id, function () use ($course) {
            $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Week 4']);
            return ['name' => get_fast_modinfo($course->id)->get_cm($page->cmid)->name, 'display' => (int) $page->display];
        });

        $this->assertTrue($result['dry_run']);
        $this->assertSame('Week 4', $result['name']);
        $this->assertSame($before, $DB->count_records('course_modules', ['course' => $course->id]));
        $this->assertCount(0, get_fast_modinfo($course->id)->get_instances_of('page'));
        $sink->close();
    }

    public function test_real_run_keeps_the_change(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $result = dry_run::run(false, $course->id, function () use ($course) {
            $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
            return ['created' => true];
        });
        $this->assertFalse($result['dry_run']);
        $this->assertSame(1, $DB->count_records('page', ['course' => $course->id]));
    }

    public function test_errors_pass_through_and_roll_back(): void {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        try {
            dry_run::run(true, $course->id, function () use ($course) {
                $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
                throw new \moodle_exception('invalidrecord', 'error');
            });
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertSame('invalidrecord', $e->errorcode);
        }
        $this->assertSame(0, $DB->count_records('page', ['course' => $course->id]));
    }
}
