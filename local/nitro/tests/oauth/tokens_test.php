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

namespace local_nitro\oauth;

/**
 * Tests for the token endpoint's logic and access token checks.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(tokens::class)]
final class tokens_test extends \advanced_testcase {
    /** @var string Redirect URI used by the test clients. */
    private const REDIRECT = 'https://claude.ai/api/mcp/auth_callback';

    /** @var string A PKCE code verifier. */
    private const VERIFIER = 'dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk';

    /** @var \stdClass Teacher allowed to connect. */
    private \stdClass $teacher;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $this->teacher = $gen->create_and_enrol($course, 'editingteacher');
        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $this->teacher->id, \context_course::instance($course->id));
    }

    /**
     * Registers a client.
     *
     * @param string $authmethod
     * @return array{0: client, 1: ?string}
     */
    private function client(string $authmethod = 'none'): array {
        return $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_client(
            ['redirecturis' => [self::REDIRECT], 'authmethod' => $authmethod]
        );
    }

    /**
     * Runs the authorization step and returns a fresh code.
     *
     * @param client $client
     * @param string|null $scope
     * @return string
     */
    private function code(client $client, ?string $scope = null): string {
        $request = authorization::validate([
            'response_type' => 'code',
            'client_id' => $client->clientid,
            'redirect_uri' => self::REDIRECT,
            'code_challenge' => tokens::s256(self::VERIFIER),
            'code_challenge_method' => 'S256',
            'scope' => $scope,
        ]);
        parse_str(parse_url(authorization::approve($request, $this->teacher->id), PHP_URL_QUERY), $query);
        return $query['code'];
    }

    /**
     * Token request parameters for a code exchange.
     *
     * @param client $client
     * @param string $code
     * @param array $overrides
     * @return array
     */
    private function exchange(client $client, string $code, array $overrides = []): array {
        return array_merge([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => self::REDIRECT,
            'code_verifier' => self::VERIFIER,
            'client_id' => $client->clientid,
        ], $overrides);
    }

    public function test_code_exchange(): void {
        global $DB;
        [$client] = $this->client();
        [$status, $response] = tokens::handle($this->exchange($client, $this->code($client)), null);

        $this->assertSame(200, $status);
        $this->assertSame('Bearer', $response['token_type']);
        $this->assertLessThanOrEqual(HOURSECS, $response['expires_in']);
        $this->assertSame('nitro offline_access', $response['scope']);
        $this->assertNotEmpty($response['refresh_token']);
        $grant = tokens::validate_access($response['access_token']);
        $this->assertEquals($this->teacher->id, $grant->userid);
        $this->assertSame($client->clientid, $grant->clientid);
        $this->assertSame('claude.ai', $grant->redirecthost);
        // Only hashes are stored.
        $this->assertFalse($DB->record_exists('local_nitro_token', ['tokenhash' => $response['access_token']]));
        $this->assertNotNull($DB->get_field('local_nitro_client', 'timefirstused', ['clientid' => $client->clientid]));
    }

    public function test_no_refresh_token_without_offline_access(): void {
        [$client] = $this->client();
        [, $response] = tokens::handle($this->exchange($client, $this->code($client, 'nitro')), null);
        $this->assertArrayNotHasKey('refresh_token', $response);
    }

    public function test_code_reuse(): void {
        [$client] = $this->client();
        $code = $this->code($client);
        $this->assertSame(200, tokens::handle($this->exchange($client, $code), null)[0]);
        [$status, $response] = tokens::handle($this->exchange($client, $code), null);
        $this->assertSame(400, $status);
        $this->assertSame('invalid_grant', $response['error']);
    }

    public function test_expired_code(): void {
        global $DB;
        [$client] = $this->client();
        $code = $this->code($client);
        $DB->set_field('local_nitro_code', 'expires', time() - 1);
        $this->assertSame('invalid_grant', tokens::handle($this->exchange($client, $code), null)[1]['error']);
    }

    /**
     * Code exchanges that must fail with invalid_grant or invalid_target.
     *
     * @return array
     */
    public static function bad_exchange_provider(): array {
        return [
            'wrong verifier' => [['code_verifier' => str_repeat('a', 43)], 'invalid_grant'],
            'missing verifier' => [['code_verifier' => ''], 'invalid_grant'],
            'other redirect URI' => [['redirect_uri' => 'https://claude.com/api/mcp/auth_callback'], 'invalid_grant'],
            'unknown code' => [['code' => 'nope'], 'invalid_grant'],
            'foreign resource' => [['resource' => 'https://evil.example/mcp'], 'invalid_target'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bad_exchange_provider')]
    public function test_bad_exchange(array $overrides, string $error): void {
        [$client] = $this->client();
        [$status, $response] = tokens::handle($this->exchange($client, $this->code($client), $overrides), null);
        $this->assertSame(400, $status);
        $this->assertSame($error, $response['error']);
    }

    public function test_code_of_another_client(): void {
        [$client] = $this->client();
        [$other] = $this->client();
        [, $response] = tokens::handle($this->exchange($other, $this->code($client)), null);
        $this->assertSame('invalid_grant', $response['error']);
    }

    public function test_confidential_client_without_secret(): void {
        [$client] = $this->client('client_secret_post');
        [$status, $response] = tokens::handle($this->exchange($client, $this->code($client)), null);
        $this->assertSame(401, $status);
        $this->assertSame('invalid_client', $response['error']);
    }

    public function test_confidential_client_with_wrong_secret(): void {
        [$client] = $this->client('client_secret_post');
        $params = $this->exchange($client, $this->code($client), ['client_secret' => 'wrong']);
        $this->assertSame('invalid_client', tokens::handle($params, null)[1]['error']);
    }

    public function test_confidential_client_with_secret_in_body(): void {
        [$client, $secret] = $this->client('client_secret_post');
        $params = $this->exchange($client, $this->code($client), ['client_secret' => $secret]);
        $this->assertSame(200, tokens::handle($params, null)[0]);
    }

    public function test_confidential_client_with_basic_auth(): void {
        [$client, $secret] = $this->client('client_secret_basic');
        $params = $this->exchange($client, $this->code($client));
        unset($params['client_id']);
        $this->assertSame(200, tokens::handle($params, [$client->clientid, $secret])[0]);
    }

    public function test_unknown_grant_type(): void {
        [$client] = $this->client();
        [$status, $response] = tokens::handle(['grant_type' => 'password', 'client_id' => $client->clientid], null);
        $this->assertSame(400, $status);
        $this->assertSame('unsupported_grant_type', $response['error']);
    }

    /**
     * Gets a token pair for a new connection.
     *
     * @param client $client
     * @return array token response
     */
    private function connect(client $client): array {
        return tokens::handle($this->exchange($client, $this->code($client)), null)[1];
    }

    public function test_refresh_rotates(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        [$status, $second] = tokens::handle(['grant_type' => 'refresh_token', 'client_id' => $client->clientid,
            'refresh_token' => $first['refresh_token']], null);

        $this->assertSame(200, $status);
        $this->assertNotSame($first['refresh_token'], $second['refresh_token']);
        $this->assertNotNull(tokens::validate_access($second['access_token']));
    }

    public function test_refresh_reuse_revokes_everything(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        $refresh = ['grant_type' => 'refresh_token', 'client_id' => $client->clientid,
            'refresh_token' => $first['refresh_token']];
        [, $second] = tokens::handle($refresh, null);

        [$status, $response] = tokens::handle($refresh, null);
        $this->assertSame(400, $status);
        $this->assertSame('invalid_grant', $response['error']);
        $this->assertNull(tokens::validate_access($first['access_token']));
        $this->assertNull(tokens::validate_access($second['access_token']));
        $this->assertSame('invalid_grant', tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => $client->clientid, 'refresh_token' => $second['refresh_token']], null)[1]['error']);
    }

    public function test_expired_refresh_token(): void {
        global $DB;
        [$client] = $this->client();
        $first = $this->connect($client);
        $DB->set_field('local_nitro_token', 'expires', time() - 1, ['tokentype' => 'refresh']);
        $this->assertSame('invalid_grant', tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => $client->clientid, 'refresh_token' => $first['refresh_token']], null)[1]['error']);
    }

    public function test_refresh_token_of_another_client(): void {
        [$client] = $this->client();
        [$other] = $this->client();
        $first = $this->connect($client);
        $this->assertSame('invalid_grant', tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => $other->clientid, 'refresh_token' => $first['refresh_token']], null)[1]['error']);
    }

    public function test_refresh_after_access_withdrawn(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        role_unassign_all(['userid' => $this->teacher->id]);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertSame('invalid_grant', tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => $client->clientid, 'refresh_token' => $first['refresh_token']], null)[1]['error']);
        $this->assertNull(tokens::validate_access($first['access_token']));
    }

    public function test_expired_access_token(): void {
        global $DB;
        [$client] = $this->client();
        $first = $this->connect($client);
        $DB->set_field('local_nitro_token', 'expires', time() - 1, ['tokentype' => 'access']);
        $this->assertNull(tokens::validate_access($first['access_token']));
    }

    public function test_suspended_user(): void {
        global $DB;
        [$client] = $this->client();
        $first = $this->connect($client);
        $DB->set_field('user', 'suspended', 1, ['id' => $this->teacher->id]);
        $this->assertNull(tokens::validate_access($first['access_token']));
    }

    public function test_deleted_user(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        delete_user($this->teacher);
        $this->assertNull(tokens::validate_access($first['access_token']));
    }

    public function test_plugin_disabled(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        set_config('active', 0, 'local_nitro');
        $this->assertNull(tokens::validate_access($first['access_token']));
        set_config('active', 1, 'local_nitro');
        $this->assertNotNull(tokens::validate_access($first['access_token']));
    }

    public function test_revoked_grant(): void {
        [$client] = $this->client();
        $first = $this->connect($client);
        $grant = tokens::validate_access($first['access_token']);
        tokens::revoke_grant($grant->id);
        $this->assertNull(tokens::validate_access($first['access_token']));
        $this->assertSame('invalid_grant', tokens::handle(['grant_type' => 'refresh_token',
            'client_id' => $client->clientid, 'refresh_token' => $first['refresh_token']], null)[1]['error']);
    }

    public function test_reconnecting_reuses_the_grant(): void {
        global $DB;
        [$client] = $this->client();
        $this->connect($client);
        $this->connect($client);
        $this->assertSame(1, $DB->count_records('local_nitro_grant', ['userid' => $this->teacher->id]));
    }

    public function test_user_without_access_cannot_exchange(): void {
        [$client] = $this->client();
        $code = $this->code($client);
        role_unassign_all(['userid' => $this->teacher->id]);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertSame('invalid_grant', tokens::handle($this->exchange($client, $code), null)[1]['error']);
    }
}
