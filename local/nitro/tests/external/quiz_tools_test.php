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
 * Tests for the quiz tools: import_questions, create_quiz, add_questions_to_quiz.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(import_questions::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(create_quiz::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(add_questions_to_quiz::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_nitro\local\qbank::class)]
final class quiz_tools_test extends tool_testcase {
    /**
     * GIFT text with multiple-choice questions.
     *
     * @param int $count
     * @return string
     */
    private static function gift(int $count): string {
        $blocks = [];
        for ($i = 1; $i <= $count; $i++) {
            $right = $i + 1;
            $blocks[] = "::Q{$i}:: Mennyi {$i}+1? {={$right} ~" . ($i + 2) . " ~" . ($i + 3) . "}";
        }
        return implode("\n\n", $blocks) . "\n";
    }

    /**
     * Imports GIFT questions.
     *
     * @param string $gift
     * @param string $category
     * @param int $quizcmid
     * @return array
     */
    private function import(string $gift, string $category = 'Week 4', int $quizcmid = 0): array {
        return import_questions::clean_returnvalue(
            import_questions::execute_returns(),
            import_questions::execute($this->course->id, 'gift', $gift, $category, $quizcmid, false)
        );
    }

    /**
     * Creates a quiz.
     *
     * @param array $args
     * @return array
     */
    private function quiz(array $args = []): array {
        $args = array_merge(['courseid' => $this->course->id, 'name' => 'Week 4 quiz', 'intro' => '', 'section' => 0,
            'key' => '', 'open' => '', 'close' => '', 'time_limit_minutes' => 0, 'attempts' => 0,
            'shuffle_questions' => false, 'review' => 'practice', 'visible' => false, 'dry_run' => false], $args);
        return create_quiz::clean_returnvalue(create_quiz::execute_returns(), create_quiz::execute(...array_values($args)));
    }

    /**
     * Adds questions to a quiz.
     *
     * @param int $cmid
     * @param array $args
     * @return array
     */
    private function add(int $cmid, array $args): array {
        $args = array_merge(['quiz_cmid' => $cmid, 'question_ids' => [], 'random_category' => '', 'random_count' => 0,
            'random_from_quiz_bank' => false, 'mark' => 1.0, 'questions_per_page' => 0, 'max_grade' => 0.0,
            'dry_run' => false], $args);
        return add_questions_to_quiz::clean_returnvalue(
            add_questions_to_quiz::execute_returns(),
            add_questions_to_quiz::execute(...array_values($args))
        );
    }

    public function test_gift_into_new_shared_bank(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->assertCount(0, get_fast_modinfo($this->course->id)->get_instances_of('qbank'));

        $result = $this->import(self::gift(15));

        $this->assertSame(15, $result['count']);
        $this->assertCount(15, $result['questions']);
        $this->assertSame('Week 4', $result['category']['name']);
        $this->assertTrue($result['category']['created']);
        $this->assertCount(1, get_fast_modinfo($this->course->id)->get_instances_of('qbank'));
        $this->assertSame(15, \local_nitro\local\qbank::count_ready($result['category']['id']));
        $this->assertSame('multichoice', $result['questions'][0]['type']);

        // Importing again reuses the category.
        $again = $this->import(self::gift(2));
        $this->assertFalse($again['category']['created']);
        $this->assertSame($result['category']['id'], $again['category']['id']);
        $this->assertSame(1, $DB->count_records('question_categories', ['name' => 'Week 4']));
    }

    public function test_parse_error_imports_nothing(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $bad = self::gift(2) . "\n::Broken:: Mi a válasz? {=igen ~nem\n\n" . self::gift(1);
        try {
            $this->import($bad);
            $this->fail('Expected an error');
        } catch (\moodle_exception $e) {
            $this->assertSame('importfailed', $e->errorcode);
            $this->assertMatchesRegularExpression('/line \d+/', $e->getMessage());
            $this->assertStringContainsString('Mi a válasz?', $e->getMessage());
            $this->assertStringNotContainsString('ERROR', $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('question_categories', ['name' => 'Week 4']));
    }

    public function test_missing_permission(): void {
        $this->setup_course(0);
        $role = \core\di::get(\moodle_database::class)->get_field('role', 'id', ['shortname' => 'editingteacher']);
        assign_capability('moodle/question:add', CAP_PROHIBIT, $role, \context_course::instance($this->course->id));
        $this->setUser($this->teacher);
        try {
            $this->import(self::gift(1));
            $this->fail('Expected an error');
        } catch (\required_capability_exception $e) {
            $this->assertStringContainsString(get_capability_string('moodle/question:add'), $e->getMessage());
        }
    }

    public function test_practice_quiz_until_friday(): void {
        global $DB;
        $this->setup_course(0);
        $DB->update_record('user', ['id' => $this->teacher->id, 'timezone' => 'Europe/Budapest', 'lang' => 'en']);
        $this->setUser($DB->get_record('user', ['id' => $this->teacher->id]));

        $result = $this->quiz(['close' => '2026-10-09T23:59', 'attempts' => 0, 'review' => 'practice']);

        $this->assertSame(['date' => '2026-10-09T23:59:00+02:00', 'weekday' => 'Friday'], $result['close']);
        $this->assertSame(0, $result['attempts']);
        $this->assertFalse($result['visible']);
        $quiz = $DB->get_record('quiz', ['course' => $this->course->id], '*', MUST_EXIST);
        $immediately = \mod_quiz\question\display_options::IMMEDIATELY_AFTER;
        $this->assertNotEmpty($quiz->reviewrightanswer & $immediately);
        $this->assertNotEmpty($quiz->reviewspecificfeedback & $immediately);
    }

    public function test_exam_preset_hides_answers_until_close(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->quiz(['review' => 'exam']);
        $quiz = $DB->get_record('quiz', ['course' => $this->course->id], '*', MUST_EXIST);
        $options = \mod_quiz\question\display_options::class;
        $this->assertEmpty($quiz->reviewrightanswer & ($options::IMMEDIATELY_AFTER | $options::LATER_WHILE_OPEN));
        $this->assertNotEmpty($quiz->reviewrightanswer & $options::AFTER_CLOSE);
    }

    public function test_random_questions_from_a_category(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->import(self::gift(15));
        $quiz = $this->quiz();

        $result = $this->add($quiz['cmid'], ['random_category' => 'week 4', 'random_count' => 10, 'mark' => 2,
            'questions_per_page' => 5]);

        $this->assertSame(10, $result['added_random']);
        $this->assertSame(10, $result['total_slots']);
        $this->assertSame(2, $result['pages']);
        $this->assertSame(20.0, $result['total_marks']);
        $quizid = $DB->get_field('quiz', 'id', ['course' => $this->course->id]);
        $this->assertSame(10, $DB->count_records('question_set_references', ['usingcontextid' =>
            \context_module::instance($quiz['cmid'])->id, 'component' => 'mod_quiz']));
        $this->assertEquals(20.0, $DB->get_field('quiz', 'sumgrades', ['id' => $quizid]));
    }

    public function test_fixed_questions(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $imported = $this->import(self::gift(3));
        $quiz = $this->quiz();
        $result = $this->add($quiz['cmid'], ['question_ids' => array_column($imported['questions'], 'id')]);
        $this->assertCount(3, $result['added_questions']);
        $this->assertSame(3, $result['total_slots']);
        $this->assertSame(3.0, $result['total_marks']);
        // The quiz is out of 3, not out of the default 10 with every mark scaled behind the teacher's back.
        $this->assertSame(3.0, $result['max_grade']);
        $this->assertTrue($result['marks_match_max_grade']);
    }

    public function test_max_grade_the_teacher_asked_for(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $imported = $this->import(self::gift(4));
        $quiz = $this->quiz();

        $result = $this->add($quiz['cmid'], [
            'question_ids' => array_column($imported['questions'], 'id'),
            'max_grade' => 10,
        ]);

        $this->assertSame(4.0, $result['total_marks']);
        $this->assertSame(10.0, $result['max_grade']);
        $this->assertFalse($result['marks_match_max_grade']);
        $this->assertEquals(10.0, $DB->get_field('quiz', 'grade', ['id' =>
            $DB->get_field('quiz', 'id', ['course' => $this->course->id])]));
    }

    public function test_not_enough_questions(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->import(self::gift(15));
        $quiz = $this->quiz();
        try {
            $this->add($quiz['cmid'], ['random_category' => 'Week 4', 'random_count' => 20]);
            $this->fail('Expected an error');
        } catch (\moodle_exception $e) {
            $this->assertSame('notenoughquestions', $e->errorcode);
            $this->assertStringContainsString('holds 15 questions', $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('quiz_slots'));
    }

    public function test_quiz_already_attempted(): void {
        $this->setup_course(1);
        $this->setUser($this->teacher);
        $imported = $this->import(self::gift(2));
        $quiz = $this->quiz(['visible' => true]);
        $this->add($quiz['cmid'], ['question_ids' => [$imported['questions'][0]['id']]]);
        $quizid = get_fast_modinfo($this->course->id)->get_cm($quiz['cmid'])->instance;
        // Attempts started by the teacher are previews; the lock is about student attempts.
        $this->setUser($this->students[0]);
        $this->getDataGenerator()->get_plugin_generator('mod_quiz')->create_attempt($quizid, $this->students[0]->id);
        $this->setUser($this->teacher);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessageMatches('/already attempted this quiz/');
        $this->add($quiz['cmid'], ['question_ids' => [$imported['questions'][1]['id']]]);
    }

    public function test_moodle_before_5(): void {
        global $CFG;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $CFG->branch = '405';
        $CFG->release = '4.5.6 (Build: 20250811)';
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessageMatches('/need Moodle 5\.0 or later; this site runs Moodle 4\.5\.6/');
        $this->import(self::gift(1));
    }

    public function test_failed_import_through_mcp_leaves_no_transaction_and_is_logged(): void {
        global $DB;
        $this->setup_course(0);
        $this->preventResetByRollback();
        $this->setUser($this->teacher);
        $sink = $this->redirectEvents();

        $result = \local_nitro\mcp\dispatcher::call(
            'import_questions',
            \local_nitro\mcp\registry::function_info('import_questions'),
            ['courseid' => $this->course->id, 'format' => 'gift', 'questions' => "::Bad:: Kérdés {=igen ~nem\n",
            'category' => 'Week 4'],
            'client1'
        );

        $this->assertTrue($result['isError']);
        $this->assertStringContainsString('line 1', $result['message']);
        $this->assertFalse($DB->is_transaction_started());
        $events = array_filter($sink->get_events(), fn($e) => $e instanceof \local_nitro\event\tool_called);
        $this->assertSame('error', reset($events)->other['outcome']);
        $this->assertSame(0, $DB->count_records('question_categories', ['name' => 'Week 4']));
        $sink->close();
    }
}
