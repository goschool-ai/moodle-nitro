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
 * Tests for the redirect URI allowlist.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(redirect_uris::class)]
final class redirect_uris_test extends \advanced_testcase {
    /**
     * Redirect URIs checked against the default allowlist.
     *
     * @return array
     */
    public static function default_list_provider(): array {
        return [
            'Claude web' => ['https://claude.ai/api/mcp/auth_callback', true],
            'Claude on claude.com' => ['https://claude.com/api/mcp/auth_callback', true],
            'Copilot' => ['https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect', true],
            'Claude Code on an ephemeral port' => ['http://localhost:53117/callback', true],
            'loopback IP on a port' => ['http://127.0.0.1:8080/callback', true],
            'loopback without port' => ['http://localhost/callback', true],
            'unknown host' => ['https://evil.example/api/mcp/auth_callback', false],
            'lookalike host' => ['https://claude.ai.evil.example/api/mcp/auth_callback', false],
            'other path on Claude' => ['https://claude.ai/other', false],
            'https Claude with a port' => ['https://claude.ai:8443/api/mcp/auth_callback', false],
            'loopback over https' => ['https://localhost:53117/callback', false],
            'loopback other path' => ['http://localhost:53117/steal', false],
            'extra query' => ['https://claude.ai/api/mcp/auth_callback?x=1', false],
            'fragment' => ['https://claude.ai/api/mcp/auth_callback#x', false],
            'userinfo' => ['http://evil@localhost:53117/callback', false],
            'not a URI' => ['claude', false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('default_list_provider')]
    public function test_default_list(string $uri, bool $expected): void {
        $this->assertSame($expected, redirect_uris::is_allowed($uri));
    }

    public function test_admin_list_replaces_defaults(): void {
        $this->resetAfterTest();
        set_config('redirecturis', "https://lms.example/cb\n\n  http://localhost/callback  ", 'local_nitro');
        $this->assertSame(['https://lms.example/cb', 'http://localhost/callback'], redirect_uris::allowlist());
        $this->assertTrue(redirect_uris::is_allowed('https://lms.example/cb'));
        $this->assertTrue(redirect_uris::is_allowed('http://localhost:1234/callback'));
        $this->assertFalse(redirect_uris::is_allowed('https://claude.ai/api/mcp/auth_callback'));
    }

    public function test_is_loopback(): void {
        $this->assertTrue(redirect_uris::is_loopback('http://127.0.0.1:5000/cb'));
        $this->assertTrue(redirect_uris::is_loopback('http://[::1]:5000/cb'));
        $this->assertFalse(redirect_uris::is_loopback('https://claude.ai/api/mcp/auth_callback'));
    }
}
