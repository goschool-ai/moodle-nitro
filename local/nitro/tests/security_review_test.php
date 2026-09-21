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

namespace local_nitro;

use local_nitro\oauth\authorization;
use local_nitro\oauth\cimd;
use local_nitro\oauth\clients;
use local_nitro\oauth\registration;
use local_nitro\oauth\tokens;

/**
 * Regression tests for the fixes from the security review (11.3).
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(local\access::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(observer::class)]
final class security_review_test extends \advanced_testcase {
    public function test_separate_groups_limit_what_a_teacher_sees(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $gen = $this->getDataGenerator();
        $course = $gen->create_course(['groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1]);
        $teacher = $gen->create_and_enrol($course, 'teacher');
        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $teacher->id, \context_course::instance($course->id));
        $mygroup = $gen->create_group(['courseid' => $course->id]);
        $other = $gen->create_group(['courseid' => $course->id]);
        $gen->create_group_member(['groupid' => $mygroup->id, 'userid' => $teacher->id]);
        $mine = $gen->create_and_enrol($course, 'student');
        $theirs = $gen->create_and_enrol($course, 'student');
        $gen->create_group_member(['groupid' => $mygroup->id, 'userid' => $mine->id]);
        $gen->create_group_member(['groupid' => $other->id, 'userid' => $theirs->id]);
        $assign = $gen->create_module('assign', ['course' => $course->id, 'grade' => 10]);
        $this->setUser($teacher);

        $people = external\list_participants::execute($course->id, 'student', 0, false, false)['participants'];
        $this->assertSame([(int) $mine->id], array_column($people, 'id'));
        $subs = external\list_submissions::execute($assign->cmid, 'all', false, 0)['students'];
        $this->assertSame([(int) $mine->id], array_column($subs, 'userid'));
        try {
            external\grade_submission::execute($assign->cmid, [['userid' => $theirs->id, 'grade' => '5',
                'feedback' => '']], '', '');
            $this->fail('Grading another group must fail');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('not a student of this assignment', $e->getMessage());
        }
        $this->expectException(\required_capability_exception::class);
        external\list_participants::execute($course->id, '', $other->id, false, false);
    }

    public function test_registration_limits(): void {
        $this->resetAfterTest();
        set_config('dcrenabled', 1, 'local_nitro');
        $uris = array_map(fn($i) => "http://localhost/callback{$i}", range(1, 11));
        [$status] = registration::handle(['redirect_uris' => $uris, 'token_endpoint_auth_method' => 'none']);
        $this->assertSame(400, $status);
    }

    public function test_cimd_url_with_query_and_token_endpoint_never_fetches(): void {
        $this->resetAfterTest();
        $this->assertFalse(cimd::is_cimd_url('https://claude.ai/oauth/x?n=1'));
        $history = [];
        $this->get_mocked_http_client($history);
        [$status, $response] = tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => 'https://claude.ai/oauth/mcp-oauth-client-metadata', 'refresh_token' => 'x'], null);
        $this->assertSame(401, $status);
        $this->assertSame('invalid_client', $response['error']);
        $this->assertCount(0, $history);
    }

    public function test_refresh_survives_a_cache_purge(): void {
        global $DB;
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $user->id, \context_system::instance());
        $clientid = 'https://claude.ai/oauth/mcp-oauth-client-metadata';
        $grant = (object) ['userid' => $user->id, 'clientid' => $clientid, 'clientname' => 'Claude',
            'redirecthost' => 'claude.ai', 'scope' => 'nitro offline_access', 'timelastused' => null,
            'timecreated' => time()];
        $grant->id = $DB->insert_record('local_nitro_grant', $grant);
        $refresh = oauth\clients::random_token();
        $DB->insert_record('local_nitro_token', ['grantid' => $grant->id, 'tokentype' => 'refresh',
            'tokenhash' => hash('sha256', $refresh), 'used' => 0, 'expires' => time() + DAYSECS,
            'timecreated' => time()]);

        // The client metadata document is not cached (a purge, a restart, or simply a day later) and the
        // token endpoint must not fetch it: the refresh still has to work, or the teacher is thrown out.
        \cache::make('local_nitro', 'cimd')->purge();
        $history = [];
        $this->get_mocked_http_client($history);

        [$status, $response] = tokens::handle(['grant_type' => 'refresh_token', 'client_id' => $clientid,
            'refresh_token' => $refresh], null);

        $this->assertSame(200, $status, 'a refresh after a cache purge must not need re-authorisation');
        $this->assertNotEmpty($response['access_token']);
        $this->assertCount(0, $history, 'the token endpoint must not fetch the metadata document');
    }

    public function test_secret_sent_two_ways_is_refused(): void {
        $this->resetAfterTest();
        [$client, $secret] = $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_client(
            ['authmethod' => 'client_secret_basic', 'redirecturis' => ['http://localhost/callback']]
        );
        [$status] = tokens::handle(
            ['grant_type' => 'refresh_token', 'refresh_token' => 'x', 'client_secret' => $secret],
            [$client->clientid, $secret]
        );
        $this->assertSame(400, $status);
    }

    public function test_loopback_warning_follows_the_presented_uri(): void {
        $this->resetAfterTest();
        [$client] = $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_client(['origin' => 'dcr',
            'redirecturis' => ['https://claude.ai/api/mcp/auth_callback', 'http://localhost/callback']]);
        $this->assertFalse($client->is_loopback_only());
        $request = authorization::validate(['response_type' => 'code', 'client_id' => $client->clientid,
            'redirect_uri' => 'http://localhost:31337/callback', 'code_challenge' => str_repeat('a', 43),
            'code_challenge_method' => 'S256']);
        $this->assertTrue(oauth\redirect_uris::is_loopback($request['redirecturi']));
    }

    public function test_password_change_revokes_connections(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_grant(['userid' => $user->id]);
        \core\event\user_password_updated::create_from_user($user)->trigger();
        $this->assertSame(0, $DB->count_records('local_nitro_grant', ['userid' => $user->id]));
        $this->assertSame(0, $DB->count_records('local_nitro_token'));
    }

    public function test_disabled_auth_method_stops_tokens(): void {
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $user = $this->getDataGenerator()->create_user(['auth' => 'email']);
        [, $token] = $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_grant(['userid' => $user->id]);
        set_config('auth', 'manual');
        \core\session\manager::gc();
        $this->assertNull(tokens::validate_access($token));
    }

    public function test_message_confirmation_pins_recipients(): void {
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $teacher = $gen->create_and_enrol($course, 'editingteacher');
        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $teacher->id, \context_course::instance($course->id));
        $gen->create_and_enrol($course, 'student');
        $this->setUser($teacher);
        $preview = external\message_students::execute($course->id, 'Szia', [], 0, true, '');
        $gen->create_and_enrol($course, 'student');
        $this->expectException(\moodle_exception::class);
        external\message_students::execute($course->id, 'Szia', [], 0, true, $preview['confirmation_token']);
    }

    public function test_kill_switch_stops_tools_called_directly(): void {
        $this->resetAfterTest();
        set_config('active', 0, 'local_nitro');
        $course = $this->getDataGenerator()->create_course();
        $this->setAdminUser();
        $this->expectException(\moodle_exception::class);
        external\course_overview::execute($course->id);
    }
}
