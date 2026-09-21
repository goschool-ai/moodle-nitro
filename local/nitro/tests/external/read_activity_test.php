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
 * Tests for read_activity.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(read_activity::class)]
final class read_activity_test extends tool_testcase {
    /**
     * Calls the tool.
     *
     * @param int $cmid
     * @param bool $html
     * @return array
     */
    private function call(int $cmid, bool $html = false): array {
        return read_activity::clean_returnvalue(read_activity::execute_returns(), read_activity::execute($cmid, $html));
    }

    public function test_page_content(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $saved = save_page::execute(
            $this->course->id,
            'week4',
            'Rekurzió',
            "# Rekurzió\n\nMinden rekurzióhoz kell **alapeset** és rekurzív lépés.\n\n- faktoriális\n- fabejárás",
            0,
            true
        );

        $result = $this->call($saved['cmid']);

        $this->assertSame('page', $result['type']);
        $this->assertSame('week4', $result['key']);
        $this->assertSame('Rekurzió', $result['name']);
        $this->assertStringContainsString('ALAPESET', $result['content'], 'bold text is upper-cased by html_to_text');
        $this->assertStringContainsString('faktoriális', $result['content']);
        $this->assertStringNotContainsString('<strong>', $result['content']);
        $this->assertSame('', $result['note']);

        $withhtml = $this->call($saved['cmid'], true);
        $this->assertStringContainsString('<strong>alapeset</strong>', $withhtml['content_html']);
    }

    public function test_assignment_description_and_dates(): void {
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $saved = save_assignment::execute(
            $this->course->id,
            'hw2',
            '2. beadandó',
            'Írj **rekurzív** bejárást.',
            0,
            true,
            ['onlinetext'],
            10,
            null,
            '2026-10-09T23:59',
            '2026-10-11T20:00'
        );

        $result = $this->call($saved['cmid']);

        $this->assertSame('assign', $result['type']);
        $this->assertStringContainsString('REKURZÍV bejárást', $result['description']);
        $this->assertSame('', $result['content']);
        $this->assertContains('duedate', array_column($result['dates'], 'type'));
    }

    public function test_activity_without_readable_text(): void {
        global $DB;
        $this->setup_course(0);
        $this->setUser($this->teacher);
        $cm = $this->getDataGenerator()->create_module('resource', ['course' => $this->course->id, 'name' => 'Diák']);
        // An uploaded file with no description at all: nothing nitro can read.
        $DB->set_field('resource', 'intro', '', ['id' => $cm->id]);
        $result = $this->call($cm->cmid);
        $this->assertSame('', $result['content']);
        $this->assertStringContainsString('keeps no text', $result['note']);
    }

    public function test_hidden_activity_needs_the_capability(): void {
        $this->setup_course(0);
        $cm = $this->getDataGenerator()->create_module('page', ['course' => $this->course->id, 'visible' => 0]);
        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $this->grant_gate($student);
        $this->setUser($student);
        // Moodle refuses the context itself for a hidden activity, before any capability check of ours.
        $this->expectException(\core\exception\require_login_exception::class);
        $this->call($cm->cmid);
    }

    public function test_requires_the_gate(): void {
        $this->setup_course(0);
        $cm = $this->getDataGenerator()->create_module('page', ['course' => $this->course->id]);
        $other = $this->getDataGenerator()->create_and_enrol($this->course, 'editingteacher');
        $this->setUser($other);
        $this->expectException(\required_capability_exception::class);
        $this->call($cm->cmid);
    }
}
