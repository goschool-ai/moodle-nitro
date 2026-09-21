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
 * Tests for dynamic client registration.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(registration::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(clients::class)]
final class registration_test extends \advanced_testcase {
    /** @var string Copilot's redirect URI. */
    private const TEAMS = 'https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect';

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('dcrenabled', 1, 'local_nitro');
    }

    public function test_copilot_registers_with_a_secret(): void {
        global $DB;
        [$status, $response] = registration::handle([
            'client_name' => 'Copilot',
            'redirect_uris' => [self::TEAMS],
            'token_endpoint_auth_method' => 'client_secret_post',
            'grant_types' => ['authorization_code', 'refresh_token'],
        ]);

        $this->assertSame(201, $status);
        $this->assertNotEmpty($response['client_secret']);
        $this->assertSame(0, $response['client_secret_expires_at']);
        $client = clients::find($response['client_id']);
        $this->assertTrue($client->is_confidential());
        $this->assertTrue(clients::verify_secret($client, $response['client_secret']));
        $this->assertFalse(clients::verify_secret($client, 'wrong'));
        // Only the hash is stored.
        $record = $DB->get_record('local_nitro_client', ['clientid' => $response['client_id']]);
        $this->assertNotSame($response['client_secret'], $record->secrethash);
        $this->assertSame(hash('sha256', $response['client_secret']), $record->secrethash);
    }

    public function test_public_registration(): void {
        [$status, $response] = registration::handle([
            'redirect_uris' => ['http://localhost:4455/callback'],
            'token_endpoint_auth_method' => 'none',
        ]);
        $this->assertSame(201, $status);
        $this->assertArrayNotHasKey('client_secret', $response);
        $this->assertFalse(clients::find($response['client_id'])->is_confidential());
    }

    public function test_default_auth_method_is_client_secret_basic(): void {
        [, $response] = registration::handle(['redirect_uris' => [self::TEAMS]]);
        $this->assertSame('client_secret_basic', $response['token_endpoint_auth_method']);
        $this->assertArrayHasKey('client_secret', $response);
    }

    /**
     * Registration requests that must be refused with 400.
     *
     * @return array
     */
    public static function invalid_request_provider(): array {
        return [
            'not an object' => [['a', 'b'], 'invalid_client_metadata'],
            'no redirect URIs' => [['token_endpoint_auth_method' => 'none'], 'invalid_redirect_uri'],
            'empty redirect URIs' => [['redirect_uris' => []], 'invalid_redirect_uri'],
            'unknown host' => [['redirect_uris' => ['https://evil.example/cb']], 'invalid_redirect_uri'],
            'one of two not allowed' => [['redirect_uris' => [self::TEAMS, 'https://evil.example/cb']], 'invalid_redirect_uri'],
            'private_key_jwt' => [['redirect_uris' => [self::TEAMS], 'token_endpoint_auth_method' => 'private_key_jwt'],
                'invalid_client_metadata'],
            'implicit grant' => [['redirect_uris' => [self::TEAMS], 'grant_types' => ['implicit']], 'invalid_client_metadata'],
            'token response type' => [['redirect_uris' => [self::TEAMS], 'response_types' => ['token']],
                'invalid_client_metadata'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalid_request_provider')]
    public function test_invalid_request(mixed $metadata, string $error): void {
        global $DB;
        [$status, $response] = registration::handle($metadata);
        $this->assertSame(400, $status);
        $this->assertSame($error, $response['error']);
        $this->assertSame(0, $DB->count_records('local_nitro_client'));
    }

    public function test_cap_reached(): void {
        set_config('dcrcap', 2, 'local_nitro');
        $request = ['redirect_uris' => [self::TEAMS], 'token_endpoint_auth_method' => 'none'];
        $this->assertSame(201, registration::handle($request)[0]);
        $this->assertSame(201, registration::handle($request)[0]);
        [$status, $response] = registration::handle($request);
        $this->assertSame(503, $status);
        $this->assertSame('temporarily_unavailable', $response['error']);

        // A client that obtained a token no longer counts.
        global $DB;
        clients::mark_used($DB->get_field_sql('SELECT MIN(clientid) FROM {local_nitro_client}'));
        $this->assertSame(201, registration::handle($request)[0]);
    }

    public function test_switched_off(): void {
        [$client] = clients::register('admin', 'Existing', [self::TEAMS], 'none');
        set_config('dcrenabled', 0, 'local_nitro');

        [$status] = registration::handle(['redirect_uris' => [self::TEAMS]]);
        $this->assertSame(403, $status);
        $this->assertArrayNotHasKey('registration_endpoint', metadata::authorization_server());
        $this->assertNotNull(clients::find($client->clientid));
    }

    public function test_client_name_is_cleaned(): void {
        [, $response] = registration::handle([
            'client_name' => '<b>Copilot</b>',
            'redirect_uris' => [self::TEAMS],
        ]);
        $this->assertSame('Copilot', $response['client_name']);
    }
}
