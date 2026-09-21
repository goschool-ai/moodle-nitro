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

use core\http_client;
use local_nitro\oauth\metadata;

/**
 * Discovery self-check: fetches the site's own discovery URLs and reports which answer correctly.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selfcheck {
    /** @var float Timeout per request, in seconds. */
    private const TIMEOUT = 3;

    /**
     * The URLs to check: plugin-served ones, then the root ones that need the rewrite.
     *
     * @return array[] each with url, kind (resource or issuer) and root (bool)
     */
    public static function targets(): array {
        global $CFG;
        $origin = self::origin();
        $path = (string) parse_url($CFG->wwwroot, PHP_URL_PATH);
        return [
            ['url' => metadata::resource_metadata_url(), 'kind' => 'resource', 'root' => false],
            ['url' => metadata::issuer() . '/.well-known/openid-configuration', 'kind' => 'issuer', 'root' => false],
            ['url' => $origin . '/.well-known/oauth-protected-resource' . $path . '/local/nitro/mcp.php',
                'kind' => 'resource', 'root' => true],
            ['url' => $origin . '/.well-known/oauth-authorization-server' . $path . '/local/nitro/oauth/metadata.php',
                'kind' => 'issuer', 'root' => true],
        ];
    }

    /**
     * Runs the check.
     *
     * @param http_client|null $client for tests; by default a client that may reach the site itself
     * @return array[] targets with an added `ok` flag and `status` text
     */
    public static function run(?http_client $client = null): array {
        // The site fetches its own public URLs; the private-address block list must not stop that.
        $client ??= new http_client(['ignoresecurity' => true]);
        $results = [];
        foreach (self::targets() as $target) {
            $target['ok'] = false;
            try {
                $response = $client->get($target['url'], ['timeout' => self::TIMEOUT, 'http_errors' => false,
                    'allow_redirects' => false]);
                $status = $response->getStatusCode();
                $doc = json_decode((string) $response->getBody(), true);
                $expected = $target['kind'] === 'resource' ? metadata::resource() : metadata::issuer();
                $actual = $doc[$target['kind'] === 'resource' ? 'resource' : 'issuer'] ?? null;
                $target['ok'] = $status === 200 && $actual === $expected;
                $target['status'] = $status === 200 && !$target['ok'] ? '200, wrong document' : (string) $status;
            } catch (\Throwable $e) {
                $target['status'] = $e->getMessage();
            }
            $results[] = $target;
        }
        return $results;
    }

    /**
     * Web server rewrite rules that map the root discovery URLs to the plugin.
     *
     * @return array{apache: string, htaccess: string, nginx: string}
     */
    public static function rewrite_rules(): array {
        global $CFG;
        $base = rtrim((string) parse_url($CFG->wwwroot, PHP_URL_PATH), '/') . '/local/nitro/oauth/metadata.php/.well-known/';
        $prm = 'oauth-protected-resource';
        $as = '(oauth-authorization-server|openid-configuration)';
        return [
            'apache' => "<IfModule mod_rewrite.c>\n    RewriteEngine On\n" .
                "    RewriteRule ^/\\.well-known/{$prm}(/.*)?\$ {$base}{$prm} [PT,L]\n" .
                "    RewriteRule ^/\\.well-known/{$as}(/.*)?\$ {$base}\$1 [PT,L]\n</IfModule>",
            'htaccess' => "<IfModule mod_rewrite.c>\n    RewriteEngine On\n" .
                "    RewriteRule ^\\.well-known/{$prm}(/.*)?\$ {$base}{$prm} [END]\n" .
                "    RewriteRule ^\\.well-known/{$as}(/.*)?\$ {$base}\$1 [END]\n</IfModule>",
            'nginx' => "rewrite ^/\\.well-known/{$prm}(/.*)?\$ {$base}{$prm} last;\n" .
                "rewrite ^/\\.well-known/{$as}(/.*)?\$ {$base}\$1 last;",
        ];
    }

    /**
     * The web server this request came through, for choosing which rule to show first.
     *
     * @return string apache, nginx or other
     */
    public static function web_server(): string {
        $software = strtolower($_SERVER['SERVER_SOFTWARE'] ?? '');
        return match (true) {
            str_contains($software, 'apache') => 'apache',
            str_contains($software, 'nginx') => 'nginx',
            default => 'other',
        };
    }

    /**
     * Scheme, host and port of the site.
     *
     * @return string
     */
    private static function origin(): string {
        global $CFG;
        $parts = parse_url($CFG->wwwroot);
        return $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
    }
}
