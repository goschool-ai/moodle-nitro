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
 * Tests for save_page and the key lookup and partial updates behind it.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(save_page::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_nitro\local\modules::class)]
final class save_page_test extends tool_testcase {
    /**
     * Calls the tool.
     *
     * @param array $args
     * @return array
     */
    private function save(array $args): array {
        $args = array_merge(['courseid' => $this->course->id, 'key' => 'requirements', 'name' => null, 'content' => null,
            'section' => null, 'visible' => null, 'dry_run' => false], $args);
        return save_page::clean_returnvalue(save_page::execute_returns(), save_page::execute(...array_values($args)));
    }

    public function test_first_publish(): void {
        global $DB;
        $this->setup_course(0, ['numsections' => 3]);
        $this->setUser($this->teacher);
        $result = $this->save(['name' => 'Követelmények', 'content' => "# Követelmények\n\nLegalább 3 beadandó.",
            'section' => 1]);

        $this->assertSame('created', $result['status']);
        $this->assertFalse($result['dry_run']);
        $this->assertSame(1, $result['section']);
        $this->assertTrue($result['visible']);
        $this->assertStringContainsString('/mod/page/view.php?id=' . $result['cmid'], $result['url']);
        $page = $DB->get_record('page', ['course' => $this->course->id], '*', MUST_EXIST);
        $this->assertStringContainsString('<h1', $page->content);
        $this->assertSame('requirements', $DB->get_field('course_modules', 'idnumber', ['id' => $result['cmid']]));
    }

    public function test_republish_updates_without_duplicate(): void {
        global $DB;
        $this->setup_course(0, ['numsections' => 3]);
        $this->setUser($this->teacher);
        $first = $this->save(['name' => 'Követelmények', 'content' => 'Régi', 'section' => 2, 'visible' => false]);
        $second = $this->save(['content' => "Új tartalom\n\n- pont"]);

        $this->assertSame('updated', $second['status']);
        $this->assertSame($first['cmid'], $second['cmid']);
        $this->assertSame(['content'], $second['changed']);
        $this->assertSame(1, $DB->count_records('page', ['course' => $this->course->id]));
        $page = $DB->get_record('page', ['course' => $this->course->id]);
        $this->assertStringContainsString('Új tartalom', $page->content);
        // Settings not in the call stay as they were.
        $this->assertSame('Követelmények', $page->name);
        $this->assertSame(2, $second['section']);
        $this->assertFalse($second['visible']);
    }

    public function test_nonexistent_section(): void {
        global $DB;
        $this->setup_course(0, ['numsections' => 3]);
        $this->setUser($this->teacher);
        try {
            $this->save(['name' => 'X', 'content' => 'Y', 'section' => 9]);
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('highest existing section number is 3', $e->getMessage());
        }
        $this->assertSame(0, $DB->count_records('page', ['course' => $this->course->id]));
    }

    public function test_create_needs_name_and_content(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches('/name and content are required/');
        $this->save(['content' => 'only content']);
    }

    public function test_dry_run_changes_nothing(): void {
        global $DB;
        $this->setup_course(0);
        $this->preventResetByRollback();
        $this->setUser($this->teacher);
        $result = $this->save(['name' => 'Próba', 'content' => "Szöveg <script>x</script>", 'dry_run' => true]);

        $this->assertTrue($result['dry_run']);
        $this->assertSame('created', $result['status']);
        $this->assertNotEmpty($result['removed_by_cleaning']);
        $this->assertSame(0, $DB->count_records('page', ['course' => $this->course->id]));
    }

    public function test_key_of_another_type(): void {
        $this->setup_course(0);
        $this->getDataGenerator()->create_module('assign', ['course' => $this->course->id, 'idnumber' => 'hw2']);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches('/belongs to a assign activity/');
        $this->save(['key' => 'hw2', 'name' => 'X', 'content' => 'Y']);
    }

    public function test_requires_manage_activities(): void {
        $this->setup_course(0);
        $teacher = $this->getDataGenerator()->create_and_enrol($this->course, 'teacher');
        $this->grant_gate($teacher);
        $this->setUser($teacher);
        $this->expectException(\required_capability_exception::class);
        $this->save(['name' => 'X', 'content' => 'Y']);
    }
}
