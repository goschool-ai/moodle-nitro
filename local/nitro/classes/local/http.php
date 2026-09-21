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

/**
 * Small helpers for reading HTTP requests.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class http {
    /**
     * The Authorization header, wherever the web server put it.
     *
     * PHP-FPM and some Apache setups do not pass it as HTTP_AUTHORIZATION; the discovery
     * self-check tells admins when it does not arrive at all.
     *
     * @return string|null
     */
    public static function authorization_header(): ?string {
        foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $key) {
            if (!empty($_SERVER[$key])) {
                return (string) $_SERVER[$key];
            }
        }
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0 && $value !== '') {
                    return (string) $value;
                }
            }
        }
        return null;
    }

    /**
     * The bearer token from the Authorization header.
     *
     * @return string|null
     */
    public static function bearer_token(): ?string {
        $header = self::authorization_header();
        if ($header !== null && preg_match('/^Bearer\s+([A-Za-z0-9._~+\/-]+=*)\s*$/i', $header, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Client credentials from HTTP Basic authentication (RFC 6749, 2.3.1).
     *
     * @return array{0: string, 1: string}|null client ID and secret
     */
    public static function basic_credentials(): ?array {
        $header = self::authorization_header();
        if ($header === null || !preg_match('/^Basic\s+([A-Za-z0-9+\/=]+)\s*$/i', $header, $m)) {
            return null;
        }
        $decoded = base64_decode($m[1], true);
        if ($decoded === false || !str_contains($decoded, ':')) {
            return null;
        }
        [$id, $secret] = explode(':', $decoded, 2);
        return [urldecode($id), urldecode($secret)];
    }
}
