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
 * Dynamic client registration (RFC 7591) as nitro accepts it.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration {
    /** @var int Largest registration request body, in bytes. */
    public const MAX_BODY = 16384;

    /** @var int Most redirect URIs one client may register. */
    public const MAX_REDIRECT_URIS = 10;

    /**
     * Handles a registration request body.
     *
     * @param mixed $metadata decoded JSON body
     * @return array{0: int, 1: array} HTTP status and response document
     */
    public static function handle(mixed $metadata): array {
        if (!get_config('local_nitro', 'dcrenabled')) {
            return [403, self::error('access_denied', 'Dynamic client registration is switched off on this site.')];
        }
        if (!is_array($metadata) || array_is_list($metadata)) {
            return [400, self::error('invalid_client_metadata', 'The request body must be a JSON object.')];
        }

        $uris = $metadata['redirect_uris'] ?? null;
        if (!is_array($uris) || !$uris || !array_is_list($uris)) {
            return [400, self::error('invalid_redirect_uri', 'redirect_uris must be a non-empty array.')];
        }
        $uris = array_values(array_unique(array_filter($uris, 'is_string')));
        if (count($uris) > self::MAX_REDIRECT_URIS) {
            return [400, self::error('invalid_redirect_uri', 'At most ' . self::MAX_REDIRECT_URIS . ' redirect_uris.')];
        }
        foreach ($uris as $uri) {
            if (!is_string($uri) || !redirect_uris::is_allowed($uri)) {
                return [400, self::error(
                    'invalid_redirect_uri',
                    'This site does not allow the redirect URI ' . (is_string($uri) ? $uri : '') . '.'
                )];
            }
        }

        // RFC 7591 default when the client does not say.
        $method = $metadata['token_endpoint_auth_method'] ?? 'client_secret_basic';
        if (!in_array($method, clients::AUTH_METHODS, true)) {
            return [400, self::error(
                'invalid_client_metadata',
                'token_endpoint_auth_method must be one of ' . implode(', ', clients::AUTH_METHODS) . '.'
            )];
        }
        $granttypes = $metadata['grant_types'] ?? ['authorization_code', 'refresh_token'];
        if (!is_array($granttypes) || array_diff($granttypes, ['authorization_code', 'refresh_token'])) {
            return [400, self::error(
                'invalid_client_metadata',
                'grant_types may contain only authorization_code and refresh_token.'
            )];
        }
        $responsetypes = $metadata['response_types'] ?? ['code'];
        if (!is_array($responsetypes) || array_diff($responsetypes, ['code'])) {
            return [400, self::error('invalid_client_metadata', 'response_types may contain only code.')];
        }

        if (clients::cap_reached()) {
            return [503, self::error(
                'temporarily_unavailable',
                'Too many clients are waiting for their first use. Try again later.'
            )];
        }

        $name = is_string($metadata['client_name'] ?? null) ? trim(clean_param($metadata['client_name'], PARAM_TEXT)) : '';
        [$client, $secret] = clients::register('dcr', $name, $uris, $method);

        $response = [
            'client_id' => $client->clientid,
            'client_id_issued_at' => time(),
            'client_name' => $client->name,
            'redirect_uris' => $client->redirecturis,
            'token_endpoint_auth_method' => $client->authmethod,
            'grant_types' => array_values($granttypes),
            'response_types' => ['code'],
        ];
        if ($secret !== null) {
            $response['client_secret'] = $secret;
            $response['client_secret_expires_at'] = 0;
        }
        return [201, $response];
    }

    /**
     * An RFC 7591 error document.
     *
     * @param string $code
     * @param string $description
     * @return array
     */
    private static function error(string $code, string $description): array {
        return ['error' => $code, 'error_description' => $description];
    }
}
