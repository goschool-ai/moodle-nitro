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
 * Tests for save_assignment.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(save_assignment::class)]
final class save_assignment_test extends tool_testcase {
    /**
     * Calls the tool.
     *
     * @param array $args
     * @return array
     */
    private function save(array $args): array {
        $args = array_merge(['courseid' => $this->course->id, 'key' => 'hw2', 'name' => null, 'description' => null,
            'section' => null, 'visible' => null, 'submission_types' => null, 'max_points' => null, 'scale' => null,
            'due' => null, 'cutoff' => null, 'dry_run' => false], $args);
        return save_assignment::clean_returnvalue(
            save_assignment::execute_returns(),
            save_assignment::execute(...array_values($args))
        );
    }

    /**
     * Course and teacher in Budapest time, English.
     */
    private function setup_teacher(): void {
        global $DB;
        $this->setup_course(1, ['numsections' => 2]);
        $DB->update_record('user', ['id' => $this->teacher->id, 'timezone' => 'Europe/Budapest', 'lang' => 'en']);
        $this->setUser($DB->get_record('user', ['id' => $this->teacher->id]));
    }

    public function test_missing_submission_type(): void {
        global $DB;
        $this->setup_teacher();
        try {
            $this->save(['name' => 'Homework 2', 'max_points' => 10]);
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('submission_types', $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('assign', ['course' => $this->course->id]));
    }

    public function test_soft_and_hard_deadline(): void {
        global $DB;
        $this->setup_teacher();
        $result = $this->save(['name' => 'Homework 2', 'description' => 'Rekurzió, **határidő** péntek.',
            'section' => 1, 'submission_types' => ['onlinetext'], 'max_points' => 10,
            'due' => '2026-10-09T23:59', 'cutoff' => '2026-10-11T20:00']);

        $this->assertSame('created', $result['status']);
        $this->assertSame(['date' => '2026-10-09T23:59:00+02:00', 'weekday' => 'Friday'], $result['due']);
        $this->assertSame(['date' => '2026-10-11T20:00:00+02:00', 'weekday' => 'Sunday'], $result['cutoff']);
        $this->assertSame(['onlinetext'], $result['submission_types']);
        $this->assertSame(['type' => 'points', 'max_points' => 10.0, 'scale' => null], $result['grading']);
        $this->assertTrue($result['feedback_comments']);
        // The due date shows in the calendar.
        $this->assertTrue($DB->record_exists('event', ['modulename' => 'assign', 'eventtype' => 'due',
            'timestart' => strtotime('2026-10-09T23:59:00+02:00')]));
    }

    public function test_cutoff_before_due(): void {
        global $DB;
        $this->setup_teacher();
        try {
            $this->save(['name' => 'Homework 2', 'submission_types' => ['file'], 'max_points' => 10,
                'due' => '2026-10-09T23:59', 'cutoff' => '2026-10-08T20:00']);
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('before the due date', $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('assign', ['course' => $this->course->id]));
    }

    public function test_description_only_update(): void {
        global $DB;
        $this->setup_teacher();
        $created = $this->save(['name' => 'Homework 2', 'section' => 1, 'visible' => false,
            'submission_types' => ['onlinetext', 'file'], 'max_points' => 20,
            'due' => '2026-10-09T23:59', 'cutoff' => '2026-10-11T20:00']);
        // A setting nitro does not manage, changed in the web UI: it must survive the update too.
        $assignid = $DB->get_field('assign', 'id', ['course' => $this->course->id]);
        $DB->set_field('assign_plugin_config', 'value', 3, ['assignment' => $assignid, 'plugin' => 'file',
            'subtype' => 'assignsubmission', 'name' => 'maxfilesubmissions']);

        $updated = $this->save(['description' => 'Új leírás a repóból.']);

        $this->assertSame('updated', $updated['status']);
        $this->assertSame($created['cmid'], $updated['cmid']);
        $this->assertSame(['intro'], $updated['changed']);
        foreach (['name', 'section', 'visible', 'submission_types', 'grading', 'due', 'cutoff', 'feedback_comments'] as $f) {
            $this->assertSame($created[$f], $updated[$f], $f);
        }
        $this->assertStringContainsString('Új leírás', $DB->get_field('assign', 'intro', ['course' => $this->course->id]));
        $this->assertSame(1, $DB->count_records('assign', ['course' => $this->course->id]));
        $this->assertSame(3, $updated['max_files']);
    }

    public function test_scale_grading(): void {
        $this->setup_teacher();
        $this->getDataGenerator()->create_scale(['name' => 'Elfogadva', 'scale' => 'Nem fogadható el,Elfogadva',
            'courseid' => $this->course->id]);
        $result = $this->save(['name' => 'Beadandó', 'submission_types' => ['onlinetext'], 'scale' => 'elfogadva']);
        $this->assertSame('scale', $result['grading']['type']);
        $this->assertSame('Elfogadva', $result['grading']['scale']);
    }

    public function test_remove_due_date(): void {
        $this->setup_teacher();
        $this->save(['name' => 'Homework 2', 'submission_types' => ['file'], 'max_points' => 10,
            'due' => '2026-10-09T23:59']);
        $result = $this->save(['due' => 'none']);
        $this->assertNull($result['due']['date']);
        $this->assertSame(['duedate'], $result['changed']);
    }
}
