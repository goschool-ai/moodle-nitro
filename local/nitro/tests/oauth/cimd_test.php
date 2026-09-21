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

use GuzzleHttp\Psr7\Response;

/**
 * Tests for Client ID Metadata Document resolution.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(cimd::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(client::class)]
final class cimd_test extends \advanced_testcase {
    /** @var string Claude's client metadata document URL. */
    private const CLAUDE = 'https://claude.ai/oauth/mcp-oauth-client-metadata';

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        \cache::make('local_nitro', 'cimd')->purge();
    }

    /**
     * A document like Claude's.
     *
     * @param array $overrides
     * @return array
     */
    private static function claude_doc(array $overrides = []): array {
        return array_merge([
            'client_id' => self::CLAUDE,
            'client_name' => 'Claude',
            'redirect_uris' => ['https://claude.ai/api/mcp/auth_callback'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'token_endpoint_auth_method' => 'none',
        ], $overrides);
    }

    /**
     * Sets up a mocked HTTP client answering with the given responses.
     *
     * @param Response[] $responses
     * @param array|null $history filled with the requests made
     */
    private function mock_http(array $responses, ?array &$history): void {
        $history = [];
        ['mock' => $mock] = $this->get_mocked_http_client($history);
        foreach ($responses as $response) {
            $mock->append($response);
        }
    }

    public function test_claude_connects_without_registering(): void {
        $history = [];
        ['mock' => $mock] = $this->get_mocked_http_client($history);
        $mock->append(new Response(200, ['Content-Type' => 'application/json'], json_encode(self::claude_doc())));

        $client = cimd::resolve(self::CLAUDE);

        $this->assertNotNull($client);
        $this->assertSame('Claude', $client->name);
        $this->assertSame('cimd', $client->origin);
        $this->assertFalse($client->is_confidential());
        $this->assertTrue($client->has_redirect_uri('https://claude.ai/api/mcp/auth_callback'));
        $this->assertFalse($client->has_redirect_uri('https://claude.com/api/mcp/auth_callback'));
        $this->assertCount(1, $history);
        $this->assertSame(self::CLAUDE, (string) $history[0]['request']->getUri());
    }

    public function test_document_is_cached(): void {
        $history = [];
        ['mock' => $mock] = $this->get_mocked_http_client($history);
        $mock->append(new Response(200, [], json_encode(self::claude_doc())));

        cimd::resolve(self::CLAUDE);
        $this->assertNotNull(cimd::resolve(self::CLAUDE));
        $this->assertCount(1, $history);
    }

    public function test_failed_lookup_is_cached_briefly(): void {
        $history = [];
        ['mock' => $mock] = $this->get_mocked_http_client($history);
        $mock->append(new Response(404));

        $this->assertNull(cimd::resolve(self::CLAUDE));
        $this->assertNull(cimd::resolve(self::CLAUDE));
        $this->assertCount(1, $history);
    }

    public function test_host_not_in_list_is_never_fetched(): void {
        $history = [];
        $this->get_mocked_http_client($history);

        $this->assertNull(cimd::resolve('https://evil.example/client.json'));
        $this->assertNull(cimd::resolve('https://claude.ai.evil.example/client.json'));
        $this->assertCount(0, $history);
    }

    /**
     * Client IDs that are not acceptable CIMD URLs even on an allowed host.
     *
     * @return array
     */
    public static function bad_url_provider(): array {
        return [
            'http' => ['http://claude.ai/oauth/mcp-oauth-client-metadata'],
            'no path' => ['https://claude.ai/'],
            'port' => ['https://claude.ai:8443/client.json'],
            'userinfo' => ['https://x@claude.ai/client.json'],
            'fragment' => ['https://claude.ai/client.json#x'],
            'not a URL' => ['claude'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bad_url_provider')]
    public function test_bad_url_is_not_cimd(string $url): void {
        $this->assertFalse(cimd::is_cimd_url($url));
    }

    public function test_admin_host_list(): void {
        set_config('cimdhosts', "Claude.ai\nclients.example.org\n", 'local_nitro');
        $this->assertSame(['claude.ai', 'clients.example.org'], cimd::hosts());
        $this->assertTrue(cimd::is_cimd_url('https://clients.example.org/app.json'));
    }

    /**
     * Documents that must be rejected.
     *
     * @return array
     */
    public static function invalid_doc_provider(): array {
        return [
            'client_id of another client' => [['client_id' => 'https://claude.ai/other']],
            'confidential only' => [['token_endpoint_auth_method' => 'private_key_jwt']],
            'no redirect URIs' => [['redirect_uris' => []]],
            'only disallowed redirect URIs' => [['redirect_uris' => ['https://evil.example/cb']]],
            'redirect URIs not a list' => [['redirect_uris' => 'https://claude.ai/api/mcp/auth_callback']],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalid_doc_provider')]
    public function test_invalid_document(array $overrides): void {
        $this->assertNull(cimd::validate(self::claude_doc($overrides), self::CLAUDE));
    }

    public function test_none_among_supported_methods_is_accepted(): void {
        $doc = self::claude_doc([
            'token_endpoint_auth_method' => 'private_key_jwt',
            'token_endpoint_auth_methods_supported' => ['private_key_jwt', 'none'],
        ]);
        $this->assertNotNull(cimd::validate($doc, self::CLAUDE));
    }

    public function test_disallowed_redirect_uris_are_dropped(): void {
        $doc = self::claude_doc(['redirect_uris' => ['https://evil.example/cb', 'https://claude.ai/api/mcp/auth_callback']]);
        $this->assertSame(['https://claude.ai/api/mcp/auth_callback'], cimd::validate($doc, self::CLAUDE)->redirecturis);
    }

    public function test_client_name_is_cleaned(): void {
        $doc = self::claude_doc(['client_name' => '<script>x</script>Claude']);
        $this->assertStringNotContainsString('<', cimd::validate($doc, self::CLAUDE)->name);
        $this->assertSame('claude.ai', cimd::validate(self::claude_doc(['client_name' => '']), self::CLAUDE)->name);
    }

    /**
     * HTTP answers that must not yield a client.
     *
     * @return array
     */
    public static function bad_response_provider(): array {
        return [
            'redirect' => [new Response(302, ['Location' => 'https://evil.example/doc.json'])],
            'server error' => [new Response(500)],
            'not JSON' => [new Response(200, [], '<html></html>')],
            'JSON list' => [new Response(200, [], '[1,2]')],
            'too large' => [new Response(200, [], str_repeat(' ', cimd::MAX_BYTES) . '{}')],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bad_response_provider')]
    public function test_bad_response(Response $response): void {
        $this->mock_http([$response], $history);
        $this->assertNull(cimd::resolve(self::CLAUDE));
        $this->assertCount(1, $history);
    }

    public function test_request_carries_no_user_data(): void {
        $this->setAdminUser();
        $this->mock_http([new Response(200, [], json_encode(self::claude_doc()))], $history);
        cimd::resolve(self::CLAUDE);
        $request = $history[0]['request'];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('', (string) $request->getBody());
        $this->assertFalse($request->hasHeader('Cookie'));
        $this->assertFalse($request->hasHeader('Authorization'));
    }
}
