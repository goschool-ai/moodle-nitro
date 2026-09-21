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
 * The authorization endpoint's logic: validating a request, issuing codes, answering the client.
 *
 * Until the client and its redirect URI are known to be good, errors are shown on the Moodle page
 * and never redirected (RFC 6749, 4.1.2.1); after that, they go back to the client with `state`.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class authorization {
    /** @var int Authorization code lifetime in seconds (spec: at most 10 minutes). */
    public const CODE_TTL = 300;

    /** @var string[] Request parameters carried from the GET request to the consent form. */
    public const PARAMS = ['response_type', 'client_id', 'redirect_uri', 'code_challenge', 'code_challenge_method',
        'state', 'scope', 'resource'];

    /**
     * Validates an authorization request.
     *
     * @param array $params request parameters (see PARAMS)
     * @return array ['error' => string, 'description' => string, 'redirect' => bool] when invalid, or
     *      ['client' => client, 'redirecturi' => string, 'scope' => string, 'state' => ?string,
     *       'codechallenge' => string] when valid
     */
    public static function validate(array $params): array {
        $clientid = (string) ($params['client_id'] ?? '');
        $redirecturi = (string) ($params['redirect_uri'] ?? '');
        if ($clientid === '' || $redirecturi === '') {
            return self::local_error('invalid_request', get_string('autherror_missingclient', 'local_nitro'));
        }
        $client = clients::find($clientid);
        if ($client === null) {
            return self::local_error('invalid_client', get_string('autherror_unknownclient', 'local_nitro'));
        }
        if (!$client->has_redirect_uri($redirecturi) || !redirect_uris::is_allowed($redirecturi)) {
            return self::local_error('invalid_request', get_string('autherror_redirecturi', 'local_nitro'));
        }

        // From here on errors go back to the client.
        if (($params['response_type'] ?? '') !== 'code') {
            return self::client_error('unsupported_response_type', 'Only response_type=code is supported.');
        }
        $challenge = (string) ($params['code_challenge'] ?? '');
        if (($params['code_challenge_method'] ?? '') !== 'S256' || !preg_match('/^[A-Za-z0-9._~-]{43,128}$/', $challenge)) {
            return self::client_error('invalid_request', 'PKCE with code_challenge_method=S256 is required.');
        }
        $resource = $params['resource'] ?? null;
        if ($resource !== null && $resource !== '' && $resource !== metadata::resource()) {
            return self::client_error('invalid_target', 'The resource must be ' . metadata::resource() . '.');
        }
        $scope = self::scope($params['scope'] ?? null);
        if ($scope === null) {
            return self::client_error('invalid_scope', 'Supported scopes: ' . implode(' ', metadata::SCOPES) . '.');
        }

        return [
            'client' => $client,
            'redirecturi' => $redirecturi,
            'scope' => $scope,
            'state' => isset($params['state']) ? (string) $params['state'] : null,
            'codechallenge' => $challenge,
        ];
    }

    /**
     * The granted scope for a requested one; every scope when none is requested, null if unknown.
     *
     * @param string|null $requested space-separated scopes
     * @return string|null
     */
    public static function scope(?string $requested): ?string {
        $scopes = array_filter(explode(' ', trim((string) $requested)));
        if (!$scopes) {
            return implode(' ', metadata::SCOPES);
        }
        if (array_diff($scopes, metadata::SCOPES)) {
            return null;
        }
        // The nitro scope is what the endpoint needs; offline_access only adds refresh tokens.
        return implode(' ', array_values(array_unique(array_merge(['nitro'], $scopes))));
    }

    /**
     * Whether a user may connect an AI client at all: holds local/nitro:use in some context.
     *
     * @param int $userid
     * @return bool
     */
    public static function user_may_connect(int $userid): bool {
        if (has_capability('local/nitro:use', \context_system::instance(), $userid)) {
            return true;
        }
        return (bool) get_user_capability_course('local/nitro:use', $userid, false, '', 'id', 1);
    }

    /**
     * Issues an authorization code for an approved request and returns the URL to send the user to.
     *
     * @param array $request a valid request from validate()
     * @param int $userid
     * @return string
     */
    public static function approve(array $request, int $userid): string {
        global $DB;
        $code = clients::random_token();
        $DB->insert_record('local_nitro_code', [
            'codehash' => hash('sha256', $code),
            'clientid' => $request['client']->clientid,
            'userid' => $userid,
            'redirecturi' => $request['redirecturi'],
            'codechallenge' => $request['codechallenge'],
            'scope' => $request['scope'],
            'clientname' => $request['client']->name,
            'expires' => time() + self::CODE_TTL,
            'timecreated' => time(),
        ]);
        return self::redirect_url(
            $request['redirecturi'],
            ['code' => $code, 'state' => $request['state'], 'iss' => metadata::issuer()]
        );
    }

    /**
     * The URL to send the user to when they deny the request.
     *
     * @param array $request a valid request from validate()
     * @return string
     */
    public static function deny(array $request): string {
        return self::redirect_url(
            $request['redirecturi'],
            ['error' => 'access_denied', 'state' => $request['state'], 'iss' => metadata::issuer()]
        );
    }

    /**
     * The redirect URL for an error that goes back to the client.
     *
     * @param array $params the original request parameters (redirect URI and state)
     * @param array $error from validate()
     * @return string
     */
    public static function error_url(array $params, array $error): string {
        return self::redirect_url((string) $params['redirect_uri'], [
            'error' => $error['error'],
            'error_description' => $error['description'],
            'state' => $params['state'] ?? null,
            'iss' => metadata::issuer(),
        ]);
    }

    /**
     * Adds query parameters to a redirect URI, keeping its own query.
     *
     * @param string $uri
     * @param array $params null values are left out
     * @return string
     */
    public static function redirect_url(string $uri, array $params): string {
        $query = http_build_query(array_filter($params, fn($v) => $v !== null), '', '&', PHP_QUERY_RFC3986);
        return $uri . (str_contains($uri, '?') ? '&' : '?') . $query;
    }

    /**
     * An error shown on the Moodle page.
     *
     * @param string $error
     * @param string $description
     * @return array
     */
    private static function local_error(string $error, string $description): array {
        return ['error' => $error, 'description' => $description, 'redirect' => false];
    }

    /**
     * An error sent back to the client.
     *
     * @param string $error
     * @param string $description
     * @return array
     */
    private static function client_error(string $error, string $description): array {
        return ['error' => $error, 'description' => $description, 'redirect' => true];
    }
}
