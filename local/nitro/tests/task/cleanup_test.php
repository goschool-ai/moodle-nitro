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

namespace local_nitro\task;

use local_nitro\oauth\clients;

/**
 * Tests for the clean-up task.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(cleanup::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(clients::class)]
final class cleanup_test extends \advanced_testcase {
    /** @var string An allowed redirect URI. */
    private const TEAMS = 'https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect';

    /**
     * Registers a client and backdates it.
     *
     * @param string $origin
     * @param int $age seconds
     * @param bool $used
     * @return string client ID
     */
    private function client(string $origin, int $age, bool $used = false): string {
        global $DB;
        [$client] = clients::register($origin, 'x', [self::TEAMS], 'none');
        $DB->set_field('local_nitro_client', 'timecreated', time() - $age, ['clientid' => $client->clientid]);
        if ($used) {
            clients::mark_used($client->clientid);
        }
        return $client->clientid;
    }

    public function test_abandoned_client_is_deleted(): void {
        $this->resetAfterTest();
        $abandoned = $this->client('dcr', 8 * DAYSECS);
        $recent = $this->client('dcr', 6 * DAYSECS);
        $used = $this->client('dcr', 30 * DAYSECS, true);
        $admin = $this->client('admin', 30 * DAYSECS);

        $this->expectOutputRegex('/Deleted 1 dynamic clients/');
        (new cleanup())->execute();

        $this->assertNull(clients::find($abandoned));
        $this->assertNotNull(clients::find($recent));
        $this->assertNotNull(clients::find($used));
        $this->assertNotNull(clients::find($admin));
    }

    public function test_expired_rows_are_deleted(): void {
        global $DB;
        $this->resetAfterTest();
        $row = ['userid' => 2, 'tool' => 'message_students', 'argshash' => str_repeat('a', 64), 'used' => 0,
            'timecreated' => time()];
        $DB->insert_record('local_nitro_confirm', $row + ['tokenhash' => str_repeat('1', 64), 'expires' => time() - 1]);
        $DB->insert_record('local_nitro_confirm', $row + ['tokenhash' => str_repeat('2', 64), 'expires' => time() + 600]);

        $this->expectOutputRegex('/Deleted 0/');
        (new cleanup())->execute();

        $this->assertSame(1, $DB->count_records('local_nitro_confirm'));
    }
}
