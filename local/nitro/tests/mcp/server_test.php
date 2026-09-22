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

namespace local_nitro\mcp;

use local_nitro\oauth\metadata;

/**
 * Tests for the MCP endpoint's request handling.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(server::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(registry::class)]
final class server_test extends \advanced_testcase {
    /** @var string A valid access token. */
    private string $token;

    /** @var \stdClass Its user. */
    private \stdClass $user;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('active', 1, 'local_nitro');
        $this->user = $this->getDataGenerator()->create_user();
        [, $this->token] = $this->getDataGenerator()->get_plugin_generator('local_nitro')
            ->create_grant(['userid' => $this->user->id]);
    }

    /**
     * Sends a JSON-RPC message with the valid token.
     *
     * @param array $message
     * @param array $headers
     * @return array the HTTP response, with the body decoded
     */
    private function post(array $message, array $headers = []): array {
        $response = server::handle('POST', '', json_encode($message), $headers, $this->token);
        $response['json'] = $response['body'] === null ? null : json_decode($response['body'], true);
        return $response;
    }

    public function test_initialize(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize',
            'params' => ['protocolVersion' => '2025-06-18', 'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'test', 'version' => '1']]]);

        $this->assertSame(200, $response['status']);
        $result = $response['json']['result'];
        $this->assertSame('nitro', $result['serverInfo']['name']);
        $this->assertNotEmpty($result['serverInfo']['version']);
        $this->assertSame('2025-06-18', $result['protocolVersion']);
        $this->assertArrayHasKey('tools', $result['capabilities']);
        $this->assertStringContainsString('list_courses', $result['instructions']);
    }

    public function test_initialize_with_unknown_version_gets_latest(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize',
            'params' => ['protocolVersion' => '2024-01-01']]);
        $this->assertSame(server::PROTOCOL_VERSIONS[0], $response['json']['result']['protocolVersion']);
    }

    public function test_notification(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']);
        $this->assertSame(202, $response['status']);
        $this->assertNull($response['body']);
    }

    public function test_unknown_method(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'id' => 7, 'method' => 'resources/list']);
        $this->assertSame(-32601, $response['json']['error']['code']);
        $this->assertSame(7, $response['json']['id']);
    }

    public function test_ping(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'id' => 'p', 'method' => 'ping']);
        $this->assertSame('{"jsonrpc":"2.0","id":"p","result":{}}', $response['body']);
    }

    public function test_get_not_supported(): void {
        $response = server::handle('GET', '', '', [], $this->token);
        $this->assertSame(405, $response['status']);
        $this->assertSame('POST', $response['headers']['Allow']);
    }

    public function test_appended_discovery_form(): void {
        $response = server::handle('GET', '/.well-known/openid-configuration', '', [], null);
        $this->assertSame(200, $response['status']);
        $this->assertSame(metadata::issuer(), json_decode($response['body'], true)['issuer']);
    }

    public function test_missing_token(): void {
        $response = server::handle('POST', '', '{"jsonrpc":"2.0","id":1,"method":"tools/list"}', [], null);
        $this->assertSame(401, $response['status']);
        $this->assertStringContainsString(
            'resource_metadata="' . metadata::resource_metadata_url() . '"',
            $response['headers']['WWW-Authenticate']
        );
        $this->assertStringNotContainsString('error=', $response['headers']['WWW-Authenticate']);
    }

    public function test_invalid_token(): void {
        $response = server::handle('POST', '', '{"jsonrpc":"2.0","id":1,"method":"tools/list"}', [], 'nope');
        $this->assertSame(401, $response['status']);
        $this->assertStringContainsString('error="invalid_token"', $response['headers']['WWW-Authenticate']);
    }

    public function test_inactive_plugin(): void {
        set_config('active', 0, 'local_nitro');
        $this->assertSame(503, $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping'])['status']);
    }

    public function test_parse_error_and_batch(): void {
        $response = server::handle('POST', '', '{not json', [], $this->token);
        $this->assertSame(400, $response['status']);
        $this->assertSame(-32700, json_decode($response['body'], true)['error']['code']);

        $batch = $this->post([['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping']]);
        $this->assertSame(-32600, $batch['json']['error']['code']);
    }

    public function test_unsupported_protocol_header(): void {
        $response = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping'], ['mcp-protocol-version' => '1999-01-01']);
        $this->assertSame(400, $response['status']);
    }

    public function test_requests_run_as_token_user(): void {
        global $USER;
        $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping']);
        $this->assertEquals($this->user->id, $USER->id);
    }

    public function test_tools_list_matches_snapshot(): void {
        global $CFG;
        // Decode as objects, so {} stays {} as on the wire.
        $tools = json_decode(server::handle(
            'POST',
            '',
            '{"jsonrpc":"2.0","id":1,"method":"tools/list"}',
            ['mcp-protocol-version' => '2025-06-18'],
            $this->token
        )['body'])->result->tools;
        $snapshot = $CFG->dirroot . '/local/nitro/tests/fixtures/tools_list.json';
        $actual = json_encode($tools, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        if (getenv('NITRO_UPDATE_SNAPSHOT')) {
            file_put_contents($snapshot, $actual);
        }
        $this->assertSame(
            file_get_contents($snapshot),
            $actual,
            'tools/list changed; if intended, rerun with NITRO_UPDATE_SNAPSHOT=1 and review the diff.'
        );
    }

    public function test_every_tool_has_a_description_and_object_schema(): void {
        $tools = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list'])['json']['result']['tools'];
        $this->assertNotEmpty($tools);
        foreach ($tools as $tool) {
            $this->assertNotEmpty($tool['description'], $tool['name']);
            $this->assertSame('object', $tool['inputSchema']['type'], $tool['name']);
        }
    }

    public function test_admin_removes_a_tool(): void {
        set_config('tools', 'course_overview', 'local_nitro');
        $tools = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list'])['json']['result']['tools'];
        $this->assertNotContains('list_courses', array_column($tools, 'name'));

        $call = $this->post(['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call',
            'params' => ['name' => 'list_courses', 'arguments' => new \stdClass()]]);
        $this->assertSame(-32602, $call['json']['error']['code']);
    }

    public function test_read_only_site(): void {
        // Unticking every tool that changes something leaves the reading tools working and nothing else.
        set_config('tools', implode(',', \local_nitro\local\tools::READ), 'local_nitro');
        $tools = $this->post(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list'])['json']['result']['tools'];
        $this->assertEqualsCanonicalizing(\local_nitro\local\tools::READ, array_column($tools, 'name'));

        $read = $this->post(['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call',
            'params' => ['name' => 'list_courses', 'arguments' => new \stdClass()]]);
        $this->assertArrayHasKey('result', $read['json']);
        $this->assertFalse($read['json']['result']['isError'] ?? false);
        foreach (array_diff(\local_nitro\local\tools::ALL, \local_nitro\local\tools::READ) as $write) {
            $call = $this->post(['jsonrpc' => '2.0', 'id' => 3, 'method' => 'tools/call',
                'params' => ['name' => $write, 'arguments' => new \stdClass()]]);
            $this->assertSame(-32602, $call['json']['error']['code'], $write);
        }
    }

    public function test_unknown_tool(): void {
        $call = $this->post(['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call',
            'params' => ['name' => 'drop_database']]);
        $this->assertSame(-32602, $call['json']['error']['code']);
    }
}
