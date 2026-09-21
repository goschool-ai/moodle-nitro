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
 * Tests for confirmation tokens.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(confirmation::class)]
final class confirmation_test extends \advanced_testcase {
    /** @var array Arguments of a previewed call. */
    private const ARGS = ['courseid' => 5, 'userids' => [3, 4], 'message' => 'Kérlek, add be a házit!'];

    /**
     * Asserts that redeeming fails with the given error code.
     *
     * @param string $code
     * @param string $tool
     * @param array $args
     */
    private function assert_refused(string $code, string $tool, array $args): void {
        try {
            confirmation::redeem($tool, $args);
            $this->fail("Expected {$code}");
        } catch (\moodle_exception $e) {
            $this->assertSame($code, $e->errorcode);
        }
    }

    public function test_confirmed_call(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $issued = confirmation::issue('message_students', self::ARGS);
        $this->assertNotEmpty($issued['token']);

        // Key order and dry_run do not matter.
        $args = array_reverse(self::ARGS, true) + ['confirmation_token' => $issued['token'], 'dry_run' => false];
        confirmation::redeem('message_students', $args);
        $this->assertTrue(true);
    }

    public function test_arguments_changed_after_preview(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $token = confirmation::issue('message_students', self::ARGS)['token'];
        $this->assert_refused(
            'confirmchanged',
            'message_students',
            ['message' => 'Más szöveg'] + self::ARGS + ['confirmation_token' => $token]
        );
        $this->assert_refused('confirmchanged', 'post_announcement', self::ARGS + ['confirmation_token' => $token]);
    }

    public function test_token_reuse(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $args = self::ARGS + ['confirmation_token' => confirmation::issue('message_students', self::ARGS)['token']];
        confirmation::redeem('message_students', $args);
        $this->assert_refused('confirmused', 'message_students', $args);
    }

    public function test_token_of_another_user(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $args = self::ARGS + ['confirmation_token' => confirmation::issue('message_students', self::ARGS)['token']];
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assert_refused('confirmunknown', 'message_students', $args);
    }

    public function test_expired_token(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $args = self::ARGS + ['confirmation_token' => confirmation::issue('message_students', self::ARGS)['token']];
        $DB->set_field('local_nitro_confirm', 'expires', time() - 1);
        $this->assert_refused('confirmexpired', 'message_students', $args);
    }

    public function test_missing_or_unknown_token(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assert_refused('confirmunknown', 'message_students', self::ARGS);
        $this->assert_refused('confirmunknown', 'message_students', self::ARGS + ['confirmation_token' => 'nope']);
    }

    public function test_expiry_within_ten_minutes(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        confirmation::issue('grade_submission', self::ARGS);
        $this->assertLessThanOrEqual(time() + 600, (int) $DB->get_field('local_nitro_confirm', 'expires', []));
    }
}
