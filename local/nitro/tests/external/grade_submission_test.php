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
 * Tests for grade_submission.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(grade_submission::class)]
final class grade_submission_test extends tool_testcase {
    /**
     * An assignment in the course.
     *
     * @param array $record
     * @return \stdClass
     */
    private function assignment(array $record): \stdClass {
        return $this->getDataGenerator()->create_module('assign', $record + ['course' => $this->course->id,
            'assignfeedback_comments_enabled' => 1, 'assignsubmission_onlinetext_enabled' => 1]);
    }

    /**
     * Previews and confirms grades.
     *
     * @param int $cmid
     * @param array $grades
     * @param string $state
     * @return array{0: array, 1: array} preview and result
     */
    private function grade(int $cmid, array $grades, string $state = ''): array {
        $clean = fn($r) => grade_submission::clean_returnvalue(grade_submission::execute_returns(), $r);
        $preview = $clean(grade_submission::execute($cmid, $grades, $state, ''));
        $done = $clean(grade_submission::execute($cmid, $grades, $state, $preview['confirmation_token']));
        return [$preview, $done];
    }

    public function test_accept_several_submissions_on_a_scale(): void {
        $this->setup_course(5);
        $scale = $this->getDataGenerator()->create_scale(['name' => 'Elfogadás', 'scale' => 'Incomplete,Complete']);
        $assign = $this->assignment(['grade' => -$scale->id]);
        $this->setUser($this->teacher);
        $grades = array_map(fn($s) => ['userid' => $s->id, 'grade' => 'Complete', 'feedback' => ''], $this->students);

        [$preview, $done] = $this->grade($assign->cmid, $grades);

        $this->assertFalse($preview['executed']);
        $this->assertSame('Complete', $preview['students'][0]['new_grade']);
        // Names, not bare IDs: the teacher approves people, not numbers.
        $bystudent = array_column($preview['students'], null, 'userid');
        foreach ($this->students as $student) {
            $this->assertSame(fullname($student), $bystudent[$student->id]['fullname']);
        }
        $saved = array_column($done['students'], null, 'userid');
        $this->assertSame(fullname($this->students[0]), $saved[$this->students[0]->id]['fullname']);
        $this->assertTrue($done['executed']);
        $this->assertCount(5, $done['students']);
        $gradinginfo = grade_get_grades($this->course->id, 'mod', 'assign', $assign->id, array_column($this->students, 'id'));
        foreach ($this->students as $student) {
            $this->assertEquals(2, $gradinginfo->items[0]->grades[$student->id]->grade);
        }
    }

    public function test_half_points_with_feedback(): void {
        global $DB;
        $this->setup_course(1);
        $assign = $this->assignment(['grade' => 10]);
        $this->setUser($this->teacher);
        $student = $this->students[0];

        [$preview, $done] = $this->grade(
            $assign->cmid,
            [['userid' => $student->id, 'grade' => '5', 'feedback' => 'Késve jött, ezért **fél pont**.']]
        );

        $this->assertNull($preview['students'][0]['current_grade']);
        $this->assertTrue($preview['visible_to_students']);
        $this->assertStringContainsString('5', $done['students'][0]['current_grade']);
        $grade = $DB->get_record('assign_grades', ['assignment' => $assign->id, 'userid' => $student->id], '*', MUST_EXIST);
        $this->assertEquals(5, $grade->grade);
        $comment = $DB->get_field('assignfeedback_comments', 'commenttext', ['grade' => $grade->id]);
        $this->assertStringContainsString('<strong>fél pont</strong>', $comment);
        $gradinginfo = grade_get_grades($this->course->id, 'mod', 'assign', $assign->id, $student->id);
        $this->assertEquals(5, $gradinginfo->items[0]->grades[$student->id]->grade);

        // Regrading without feedback keeps the comment.
        $this->grade($assign->cmid, [['userid' => $student->id, 'grade' => '6', 'feedback' => '']]);
        $this->assertStringContainsString('fél pont', $DB->get_field(
            'assignfeedback_comments',
            'commenttext',
            ['grade' => $grade->id]
        ));
    }

    /**
     * Grades that are not valid, and the text the error must contain.
     *
     * @return array
     */
    public static function invalid_grade_provider(): array {
        return [
            'above maximum' => ['11', 'from 0 to 10'],
            'negative' => ['-1', 'from 0 to 10'],
            'not a number' => ['jó', 'from 0 to 10'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalid_grade_provider')]
    public function test_invalid_points(string $grade, string $expected): void {
        global $DB;
        $this->setup_course(2);
        $assign = $this->assignment(['grade' => 10]);
        $this->setUser($this->teacher);
        try {
            grade_submission::execute($assign->cmid, [
                ['userid' => $this->students[0]->id, 'grade' => '8', 'feedback' => ''],
                ['userid' => $this->students[1]->id, 'grade' => $grade, 'feedback' => ''],
            ], '', '');
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString($expected, $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('local_nitro_confirm'));
    }

    public function test_unknown_scale_item_lists_valid_values(): void {
        $this->setup_course(1);
        $scale = $this->getDataGenerator()->create_scale(['name' => 'Elfogadás', 'scale' => 'Incomplete,Complete']);
        $assign = $this->assignment(['grade' => -$scale->id]);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches('/Valid values: Incomplete, Complete/');
        grade_submission::execute($assign->cmid, [['userid' => $this->students[0]->id, 'grade' => 'Excellent',
            'feedback' => '']], '', '');
    }

    public function test_marking_workflow(): void {
        global $DB;
        $this->setup_course(1);
        $assign = $this->assignment(['grade' => 10, 'markingworkflow' => 1]);
        $this->setUser($this->teacher);
        $sink = $this->redirectMessages();

        [$preview, $done] = $this->grade($assign->cmid, [['userid' => $this->students[0]->id, 'grade' => '9',
            'feedback' => '']]);

        $this->assertFalse($preview['visible_to_students']);
        $this->assertFalse($preview['students_notified']);
        $this->assertStringContainsString('do not see them until they are released', $preview['visibility_note']);
        $this->assertSame('readyforrelease', $DB->get_field(
            'assign_user_flags',
            'workflowstate',
            ['assignment' => $assign->id, 'userid' => $this->students[0]->id]
        ));
        $this->assertCount(0, $sink->get_messages());
        $this->assertTrue($done['executed']);
    }

    public function test_student_of_another_course(): void {
        $this->setup_course(1);
        $assign = $this->assignment(['grade' => 10]);
        $outsider = $this->getDataGenerator()->create_user();
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches("/User {$outsider->id} is not a student of this assignment/");
        grade_submission::execute($assign->cmid, [['userid' => $outsider->id, 'grade' => '5', 'feedback' => '']], '', '');
    }
}
