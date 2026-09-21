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
 * Tests for the discovery documents.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(metadata::class)]
final class metadata_test extends \advanced_testcase {
    public function test_protected_resource_names_the_mcp_endpoint(): void {
        global $CFG;
        $doc = metadata::protected_resource();
        $this->assertSame($CFG->wwwroot . '/local/nitro/mcp.php', $doc['resource']);
        $this->assertSame([metadata::issuer()], $doc['authorization_servers']);
        $this->assertSame(['nitro', 'offline_access'], $doc['scopes_supported']);
    }

    public function test_authorization_server_metadata(): void {
        $this->resetAfterTest();
        set_config('dcrenabled', 1, 'local_nitro');
        $doc = metadata::authorization_server();
        $this->assertSame(metadata::issuer(), $doc['issuer']);
        $this->assertStringEndsWith('/local/nitro/oauth/authorize.php', $doc['authorization_endpoint']);
        $this->assertStringEndsWith('/local/nitro/oauth/token.php', $doc['token_endpoint']);
        $this->assertStringEndsWith('/local/nitro/oauth/register.php', $doc['registration_endpoint']);
        $this->assertSame(['authorization_code', 'refresh_token'], $doc['grant_types_supported']);
        $this->assertSame(['S256'], $doc['code_challenge_methods_supported']);
        $this->assertSame(
            ['none', 'client_secret_post', 'client_secret_basic'],
            $doc['token_endpoint_auth_methods_supported']
        );
        $this->assertTrue($doc['client_id_metadata_document_supported']);
        $this->assertSame(['code'], $doc['response_types_supported']);
        $this->assertArrayNotHasKey('id_token_signing_alg_values_supported', $doc);
    }

    public function test_registration_endpoint_hidden_when_dcr_is_off(): void {
        $this->resetAfterTest();
        set_config('dcrenabled', 0, 'local_nitro');
        $this->assertArrayNotHasKey('registration_endpoint', metadata::authorization_server());
    }

    /**
     * Paths a client or a rewrite rule may use, and the document each must return.
     *
     * @return array
     */
    public static function path_provider(): array {
        return [
            'issuer itself' => ['', 'issuer'],
            'appended openid' => ['/.well-known/openid-configuration', 'issuer'],
            'appended oauth-as' => ['/.well-known/oauth-authorization-server', 'issuer'],
            '401 pointer' => ['/.well-known/oauth-protected-resource', 'resource'],
            'root rewrite, insert form' => ['/.well-known/oauth-protected-resource/local/nitro/mcp.php', 'resource'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('path_provider')]
    public function test_for_path(string $path, string $key): void {
        $this->assertArrayHasKey($key, metadata::for_path($path));
    }
}
