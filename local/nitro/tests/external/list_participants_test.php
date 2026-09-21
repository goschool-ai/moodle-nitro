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
 * Tests for list_participants.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(list_participants::class)]
final class list_participants_test extends tool_testcase {
    /**
     * Calls the tool and cleans its result.
     *
     * @param array $args
     * @return array participants keyed by user ID
     */
    private function call(array $args = []): array {
        $args = array_merge(['courseid' => $this->course->id, 'role' => '', 'groupid' => 0, 'never_accessed' => false,
            'include_identity' => false], $args);
        $result = list_participants::clean_returnvalue(
            list_participants::execute_returns(),
            list_participants::execute(...array_values($args))
        );
        return array_column($result['participants'], null, 'id');
    }

    public function test_default_list_has_no_identity_fields(): void {
        $this->setup_course(2);
        $this->setUser($this->teacher);
        $participants = $this->call();
        $this->assertCount(3, $participants);
        $student = $participants[$this->students[0]->id];
        $this->assertSame(['student'], $student['roles']);
        $this->assertNull($student['lastaccess']);
        $this->assertArrayNotHasKey('email', $student);
        $this->assertArrayNotHasKey('idnumber', $student);
    }

    public function test_filter_by_role(): void {
        $this->setup_course(2);
        $this->setUser($this->teacher);
        $participants = $this->call(['role' => 'student']);
        $this->assertEqualsCanonicalizing(array_column($this->students, 'id'), array_keys($participants));
    }

    public function test_never_accessed(): void {
        global $DB;
        $this->setup_course(3);
        $DB->insert_record('user_lastaccess', ['userid' => $this->students[0]->id, 'courseid' => $this->course->id,
            'timeaccess' => time() - HOURSECS]);
        $this->setUser($this->teacher);

        $participants = $this->call(['role' => 'student', 'never_accessed' => true]);
        $this->assertEqualsCanonicalizing([$this->students[1]->id, $this->students[2]->id], array_keys($participants));
        $all = $this->call(['role' => 'student']);
        $this->assertNotNull($all[$this->students[0]->id]['lastaccess']);
    }

    public function test_explicit_identity_request(): void {
        $this->setup_course(1);
        set_config('showuseridentity', 'email');
        $this->setUser($this->teacher);
        $student = $this->call(['include_identity' => true])[$this->students[0]->id];
        $this->assertSame('diak1@example.com', $student['email']);
        $this->assertArrayNotHasKey('idnumber', $student);
    }

    public function test_groups(): void {
        $this->setup_course(2);
        $gen = $this->getDataGenerator();
        $group = $gen->create_group(['courseid' => $this->course->id, 'name' => 'Csoport A']);
        $gen->create_group_member(['groupid' => $group->id, 'userid' => $this->students[0]->id]);
        $this->setUser($this->teacher);

        $participants = $this->call(['groupid' => $group->id]);
        $this->assertSame([(int) $this->students[0]->id], array_keys($participants));
        $this->assertSame(['Csoport A'], $participants[$this->students[0]->id]['groups']);
    }

    public function test_unknown_role(): void {
        $this->setup_course(1);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->call(['role' => 'wizard']);
    }
}
