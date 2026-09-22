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

namespace local_nitro\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

/**
 * Tests for the privacy provider.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(provider::class)]
final class provider_test extends \core_privacy\tests\provider_testcase {
    public function test_export_and_delete(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $gen = $this->getDataGenerator()->get_plugin_generator('local_nitro');
        $gen->create_grant(['userid' => $user->id, 'clientname' => 'Claude']);
        $gen->create_grant(['userid' => $other->id]);
        $context = \context_user::instance($user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertSame([$context->id], array_map('intval', $contextlist->get_contextids()));

        $approved = new approved_contextlist($user, 'local_nitro', [$context->id]);
        provider::export_user_data($approved);
        $data = writer::with_context($context)->get_data([get_string('connections', 'local_nitro')]);
        $this->assertSame('Claude', $data->connections[0]['client']);

        $DB->insert_record('local_nitro_code', ['codehash' => 'c1', 'clientid' => 'x', 'userid' => $user->id,
            'redirecturi' => 'http://localhost/callback', 'codechallenge' => 'y', 'scope' => 'nitro',
            'expires' => time() + 600, 'timecreated' => time()]);
        $DB->insert_record('local_nitro_confirm', ['userid' => $user->id, 'tool' => 'message_students',
            'argshash' => 'a', 'tokenhash' => 't', 'used' => 0, 'expires' => time() + 600, 'timecreated' => time()]);

        provider::delete_data_for_user($approved);
        $this->assertSame(0, $DB->count_records('local_nitro_code', ['userid' => $user->id]));
        $this->assertSame(0, $DB->count_records('local_nitro_confirm', ['userid' => $user->id]));
        $this->assertSame(0, $DB->count_records('local_nitro_grant', ['userid' => $user->id]));
        $this->assertSame(1, $DB->count_records('local_nitro_grant', ['userid' => $other->id]));
        $this->assertSame(1, $DB->count_records('local_nitro_token'));
    }

    public function test_metadata_declares_what_leaves_moodle(): void {
        $items = provider::get_metadata(new \core_privacy\local\metadata\collection('local_nitro'))->get_collection();
        $byname = [];
        foreach ($items as $item) {
            $byname[$item->get_name()] = $item;
        }
        foreach (
            ['local_nitro_grant', 'local_nitro_client', 'local_nitro_token', 'local_nitro_code',
                'local_nitro_confirm'] as $table
        ) {
            $this->assertArrayHasKey($table, $byname, $table);
        }
        // Course and user data go to the connected AI client; the declaration has to say so.
        $this->assertInstanceOf(\core_privacy\local\metadata\types\external_location::class, $byname['aiclient']);
        $this->assertEqualsCanonicalizing(
            ['coursecontent', 'participants', 'submissions', 'grades'],
            array_keys($byname['aiclient']->get_privacy_fields())
        );
        $this->assertArrayHasKey('feedback', $byname);
    }
}
