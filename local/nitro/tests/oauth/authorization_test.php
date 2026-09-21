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
 * Tests for the authorization endpoint's logic.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(authorization::class)]
final class authorization_test extends \advanced_testcase {
    /** @var string A valid S256 challenge. */
    private const CHALLENGE = 'E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM';

    /** @var client The registered test client. */
    private client $client;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        [$this->client] = $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_client([
            'name' => 'Test AI',
            'redirecturis' => ['https://claude.ai/api/mcp/auth_callback'],
        ]);
    }

    /**
     * A valid request, with overrides.
     *
     * @param array $overrides null values remove a parameter
     * @return array
     */
    private function params(array $overrides = []): array {
        $params = array_merge([
            'response_type' => 'code',
            'client_id' => $this->client->clientid,
            'redirect_uri' => 'https://claude.ai/api/mcp/auth_callback',
            'code_challenge' => self::CHALLENGE,
            'code_challenge_method' => 'S256',
            'state' => 'xyz',
            'resource' => metadata::resource(),
        ], $overrides);
        return array_filter($params, fn($v) => $v !== null);
    }

    public function test_valid_request(): void {
        $request = authorization::validate($this->params());
        $this->assertArrayNotHasKey('error', $request);
        $this->assertSame('Test AI', $request['client']->name);
        $this->assertSame('nitro offline_access', $request['scope']);
    }

    public function test_user_approves(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $url = authorization::approve(authorization::validate($this->params()), $user->id);

        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertStringStartsWith('https://claude.ai/api/mcp/auth_callback?', $url);
        $this->assertSame('xyz', $query['state']);
        $this->assertSame(metadata::issuer(), $query['iss']);
        $row = $DB->get_record('local_nitro_code', ['codehash' => hash('sha256', $query['code'])], '*', MUST_EXIST);
        $this->assertEquals($user->id, $row->userid);
        $this->assertSame(self::CHALLENGE, $row->codechallenge);
        $this->assertLessThanOrEqual(time() + 600, (int) $row->expires);
        $this->assertFalse($DB->record_exists('local_nitro_code', ['codehash' => $query['code']]));
    }

    public function test_user_denies(): void {
        $url = authorization::deny(authorization::validate($this->params()));
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame('access_denied', $query['error']);
        $this->assertSame('xyz', $query['state']);
    }

    /**
     * Requests rejected on the Moodle page, never redirected.
     *
     * @return array
     */
    public static function local_error_provider(): array {
        return [
            'unknown client' => [['client_id' => 'nope'], 'invalid_client'],
            'missing client' => [['client_id' => null], 'invalid_request'],
            'missing redirect URI' => [['redirect_uri' => null], 'invalid_request'],
            'redirect URI not registered' => [['redirect_uri' => 'https://claude.com/api/mcp/auth_callback'], 'invalid_request'],
            'redirect URI on another host' => [['redirect_uri' => 'https://evil.example/cb'], 'invalid_request'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('local_error_provider')]
    public function test_local_error(array $overrides, string $error): void {
        $result = authorization::validate($this->params($overrides));
        $this->assertSame($error, $result['error']);
        $this->assertFalse($result['redirect']);
    }

    public function test_redirect_uri_removed_from_allowlist_later(): void {
        set_config('redirecturis', 'http://localhost/callback', 'local_nitro');
        $result = authorization::validate($this->params());
        $this->assertSame('invalid_request', $result['error']);
        $this->assertFalse($result['redirect']);
    }

    /**
     * Requests rejected with a redirect back to the client.
     *
     * @return array
     */
    public static function client_error_provider(): array {
        return [
            'missing PKCE' => [['code_challenge' => null, 'code_challenge_method' => null], 'invalid_request'],
            'plain PKCE' => [['code_challenge_method' => 'plain'], 'invalid_request'],
            'PKCE method missing' => [['code_challenge_method' => null], 'invalid_request'],
            'challenge too short' => [['code_challenge' => 'abc'], 'invalid_request'],
            'implicit flow' => [['response_type' => 'token'], 'unsupported_response_type'],
            'foreign resource' => [['resource' => 'https://evil.example/mcp'], 'invalid_target'],
            'unknown scope' => [['scope' => 'nitro admin'], 'invalid_scope'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('client_error_provider')]
    public function test_client_error(array $overrides, string $error): void {
        $params = $this->params($overrides);
        $result = authorization::validate($params);
        $this->assertSame($error, $result['error']);
        $this->assertTrue($result['redirect']);
        parse_str(parse_url(authorization::error_url($params, $result), PHP_URL_QUERY), $query);
        $this->assertSame($error, $query['error']);
        $this->assertSame('xyz', $query['state']);
    }

    public function test_resource_may_be_omitted(): void {
        $this->assertArrayNotHasKey('error', authorization::validate($this->params(['resource' => null])));
    }

    /**
     * Requested and granted scopes.
     *
     * @return array
     */
    public static function scope_provider(): array {
        return [
            'none requested' => [null, 'nitro offline_access'],
            'empty' => ['', 'nitro offline_access'],
            'nitro only' => ['nitro', 'nitro'],
            'offline only' => ['offline_access', 'nitro offline_access'],
            'both' => ['offline_access nitro', 'nitro offline_access'],
            'unknown' => ['openid', null],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('scope_provider')]
    public function test_scope(?string $requested, ?string $granted): void {
        $this->assertSame($granted, authorization::scope($requested));
    }

    public function test_user_may_connect(): void {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $teacher = $gen->create_and_enrol($course, 'editingteacher');
        $student = $gen->create_and_enrol($course, 'student');
        $this->assertFalse(authorization::user_may_connect($teacher->id));

        $roleid = $gen->create_role();
        assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $teacher->id, \context_course::instance($course->id));

        $this->assertTrue(authorization::user_may_connect($teacher->id));
        $this->assertFalse(authorization::user_may_connect($student->id));
        $this->assertTrue(authorization::user_may_connect(get_admin()->id));
    }

    public function test_redirect_url_keeps_existing_query(): void {
        $this->assertSame(
            'https://a.example/cb?x=1&code=c%20d',
            authorization::redirect_url('https://a.example/cb?x=1', ['code' => 'c d', 'state' => null])
        );
    }

    public function test_loopback_only_client(): void {
        [$native] = $this->getDataGenerator()->get_plugin_generator('local_nitro')->create_client(
            ['redirecturis' => ['http://localhost/callback']]
        );
        $this->assertTrue($native->is_loopback_only());
        $this->assertFalse($this->client->is_loopback_only());
        $request = authorization::validate($this->params([
            'client_id' => $native->clientid,
            'redirect_uri' => 'http://localhost:53117/callback',
        ]));
        $this->assertArrayNotHasKey('error', $request);
    }
}
