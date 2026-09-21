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

use core\http_client;

/**
 * Client ID Metadata Documents: a client whose `client_id` is an HTTPS URL describes itself in
 * a JSON document at that URL (how Claude connects without registering).
 *
 * Documents are fetched only from the admin's CIMD host list, through Moodle's HTTP client (which
 * applies the site's blocked hosts), without following redirects, with a short timeout and a size
 * limit, and cached for a day. Failed lookups are cached briefly, so a bad URL cannot make the
 * site fetch repeatedly.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cimd {
    /** @var int Largest document accepted, in bytes. */
    public const MAX_BYTES = 51200;

    /** @var int How long a valid document is cached, in seconds (spec: at most 24 hours). */
    public const TTL = DAYSECS;

    /** @var int How long a failed lookup is cached, in seconds. */
    public const FAILURE_TTL = 60;

    /** @var float Request timeout in seconds; endpoints must answer within 2 s. */
    private const TIMEOUT = 1.5;

    /**
     * Whether a client ID is a URL on an allowed CIMD host (and so must be resolved here).
     *
     * @param string $clientid
     * @return bool
     */
    public static function is_cimd_url(string $clientid): bool {
        $parts = parse_url($clientid);
        if (
            $parts === false || strtolower($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])
                || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment']) || isset($parts['query'])
                || isset($parts['port']) || ($parts['path'] ?? '/') === '/'
        ) {
            return false;
        }
        return in_array(strtolower($parts['host']), self::hosts(), true);
    }

    /**
     * Whether a client ID looks like a URL at all (so it is never looked up in the client table).
     *
     * @param string $clientid
     * @return bool
     */
    public static function looks_like_url(string $clientid): bool {
        return (bool) preg_match('~^[a-z][a-z0-9+.-]*://~i', $clientid);
    }

    /**
     * The admin's CIMD host list.
     *
     * @return string[]
     */
    public static function hosts(): array {
        $lines = preg_split('/\R/', strtolower((string) get_config('local_nitro', 'cimdhosts')));
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Resolves a CIMD client, from the cache or by fetching its document.
     *
     * @param string $url the client ID
     * @param bool $fetch false to use the cache only (the token endpoint: authorize resolved it minutes ago)
     * @return client|null null if the URL is not allowed or the document is missing or invalid
     */
    public static function resolve(string $url, bool $fetch = true): ?client {
        if (!self::is_cimd_url($url)) {
            return null;
        }
        $cache = \cache::make('local_nitro', 'cimd');
        $key = sha1($url);
        $cached = $cache->get($key);
        if ($cached !== false && $cached['expires'] > time()) {
            return $cached['client'] === null ? null : self::client_from_array($cached['client']);
        }
        if (!$fetch) {
            return null;
        }

        $doc = self::fetch($url);
        $client = $doc === null ? null : self::validate($doc, $url);
        $cache->set($key, [
            'expires' => time() + ($client === null ? self::FAILURE_TTL : self::TTL),
            'client' => $client === null ? null : (array) $client,
        ]);
        return $client;
    }

    /**
     * Fetches and decodes a document; null on any failure.
     *
     * @param string $url
     * @return array|null
     */
    private static function fetch(string $url): ?array {
        try {
            $response = \core\di::get(http_client::class)->get($url, [
                'timeout' => self::TIMEOUT,
                'connect_timeout' => self::TIMEOUT,
                'allow_redirects' => false,
                'http_errors' => false,
                'stream' => true,
                'headers' => ['Accept' => 'application/json'],
            ]);
            if ($response->getStatusCode() !== 200) {
                return null;
            }
            $stream = $response->getBody();
            $body = '';
            while (!$stream->eof() && strlen($body) <= self::MAX_BYTES) {
                $body .= $stream->read(self::MAX_BYTES + 1 - strlen($body));
            }
            if (strlen($body) > self::MAX_BYTES) {
                return null;
            }
        } catch (\Throwable $e) {
            // Blocked host, timeout, DNS or TLS failure: all mean "cannot resolve this client".
            return null;
        }
        $doc = json_decode($body, true);
        return is_array($doc) && !array_is_list($doc) ? $doc : null;
    }

    /**
     * Validates a document and turns it into a client; null if it is not usable.
     *
     * @param array $doc
     * @param string $url the URL the document was fetched from
     * @return client|null
     */
    public static function validate(array $doc, string $url): ?client {
        // The document must name itself, or anyone could serve another client's metadata.
        if (($doc['client_id'] ?? null) !== $url) {
            return null;
        }
        // We never authenticate CIMD clients, so the client must be able to act as a public one.
        $method = $doc['token_endpoint_auth_method'] ?? 'none';
        $supported = $doc['token_endpoint_auth_methods_supported'] ?? [];
        if ($method !== 'none' && !(is_array($supported) && in_array('none', $supported, true))) {
            return null;
        }
        $uris = $doc['redirect_uris'] ?? null;
        if (!is_array($uris)) {
            return null;
        }
        // Keep only the allowlisted redirect URIs; a client may declare more than this site allows.
        $allowed = array_values(array_filter(
            $uris,
            fn($uri) => is_string($uri) && redirect_uris::is_allowed($uri)
        ));
        if (!$allowed) {
            return null;
        }
        $name = is_string($doc['client_name'] ?? null) ? clean_param($doc['client_name'], PARAM_TEXT) : '';
        $name = \core_text::substr(trim($name), 0, 255);
        return new client(
            clientid: $url,
            name: $name !== '' ? $name : (string) parse_url($url, PHP_URL_HOST),
            redirecturis: $allowed,
            authmethod: 'none',
            origin: 'cimd',
        );
    }

    /**
     * Rebuilds a cached client.
     *
     * @param array $data
     * @return client
     */
    private static function client_from_array(array $data): client {
        return new client(...$data);
    }
}
