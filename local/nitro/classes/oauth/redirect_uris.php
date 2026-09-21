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
 * The site's redirect URI allowlist.
 *
 * An entry matches a URI exactly, except that loopback entries (http on localhost, 127.0.0.1
 * or [::1]) match on any port, because native clients such as Claude Code listen on an
 * ephemeral port.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redirect_uris {
    /** @var string[] Hosts treated as loopback. */
    private const LOOPBACK_HOSTS = ['localhost', '127.0.0.1', '[::1]'];

    /**
     * The allowlist from the site settings.
     *
     * @return string[]
     */
    public static function allowlist(): array {
        $lines = preg_split('/\R/', (string) get_config('local_nitro', 'redirecturis'));
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Whether a URI is on the allowlist.
     *
     * @param string $uri
     * @return bool
     */
    public static function is_allowed(string $uri): bool {
        foreach (self::allowlist() as $entry) {
            if (self::matches($uri, $entry)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether a presented redirect URI matches a registered or allowlisted one.
     *
     * @param string $presented
     * @param string $registered
     * @return bool
     */
    public static function matches(string $presented, string $registered): bool {
        if ($presented === $registered) {
            return true;
        }
        $p = self::parse($presented);
        $r = self::parse($registered);
        if ($p === null || $r === null || !self::is_loopback_parts($p) || !self::is_loopback_parts($r)) {
            return false;
        }
        unset($p['port'], $r['port']);
        return $p == $r;
    }

    /**
     * Whether a URI is a loopback redirect (the client runs on the user's own computer).
     *
     * @param string $uri
     * @return bool
     */
    public static function is_loopback(string $uri): bool {
        $parts = self::parse($uri);
        return $parts !== null && self::is_loopback_parts($parts);
    }

    /**
     * Host of a redirect URI, for the consent screen and the connections page.
     *
     * @param string $uri
     * @return string
     */
    public static function host(string $uri): string {
        return (string) (parse_url($uri, PHP_URL_HOST) ?? '');
    }

    /**
     * Splits a URI into the parts that must match; null if it is not an absolute URI.
     *
     * Fragments are never allowed in redirect URIs (RFC 6749, 3.1.2).
     *
     * @param string $uri
     * @return array|null
     */
    private static function parse(string $uri): ?array {
        $parts = parse_url($uri);
        if (
            $parts === false || empty($parts['scheme']) || empty($parts['host']) || isset($parts['fragment'])
                || isset($parts['user']) || isset($parts['pass'])
        ) {
            return null;
        }
        return [
            'scheme' => strtolower($parts['scheme']),
            'host' => strtolower($parts['host']),
            'port' => $parts['port'] ?? null,
            'path' => $parts['path'] ?? '',
            'query' => $parts['query'] ?? null,
        ];
    }

    /**
     * Whether parsed URI parts are a loopback http URI.
     *
     * @param array $parts
     * @return bool
     */
    private static function is_loopback_parts(array $parts): bool {
        return $parts['scheme'] === 'http' && in_array($parts['host'], self::LOOPBACK_HOSTS, true);
    }
}
