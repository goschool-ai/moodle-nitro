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

namespace local_nitro\local;

use GuzzleHttp\Psr7\Response;
use local_nitro\oauth\metadata;

/**
 * Tests for the discovery self-check.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(selfcheck::class)]
final class selfcheck_test extends \advanced_testcase {
    public function test_site_without_rewrite(): void {
        $this->resetAfterTest();
        $history = [];
        ['client' => $client, 'mock' => $mock] = $this->get_mocked_http_client($history);
        $mock->append(new Response(200, [], json_encode(metadata::protected_resource())));
        $mock->append(new Response(200, [], json_encode(metadata::authorization_server())));
        $mock->append(new Response(404));
        $mock->append(new Response(404));

        $results = selfcheck::run($client);

        $this->assertSame([true, true, false, false], array_column($results, 'ok'));
        $this->assertSame([false, false, true, true], array_column($results, 'root'));
        $this->assertSame('404', $results[2]['status']);
        $this->assertStringEndsWith(
            '/.well-known/oauth-protected-resource' . parse_url(metadata::resource(), PHP_URL_PATH),
            $results[2]['url']
        );
    }

    public function test_wrong_document_is_not_ok(): void {
        $this->resetAfterTest();
        ['client' => $client, 'mock' => $mock] = $this->get_mocked_http_client();
        for ($i = 0; $i < 4; $i++) {
            $mock->append(new Response(200, [], '{"issuer":"https://elsewhere.example"}'));
        }
        $results = selfcheck::run($client);
        $this->assertSame([false, false, false, false], array_column($results, 'ok'));
        $this->assertSame('200, wrong document', $results[1]['status']);
    }

    public function test_rewrite_rules_follow_wwwroot_path(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->wwwroot = 'https://lms.example/moodle';
        $rules = selfcheck::rewrite_rules();
        $this->assertStringContainsString(
            '/moodle/local/nitro/oauth/metadata.php/.well-known/oauth-protected-resource',
            $rules['apache']
        );
        $this->assertStringContainsString('[END]', $rules['htaccess']);
        $this->assertStringContainsString(' last;', $rules['nginx']);
        $this->assertStringContainsString(
            'https://lms.example/.well-known/oauth-protected-resource/moodle/local/nitro/mcp.php',
            selfcheck::targets()[2]['url']
        );
    }
}
