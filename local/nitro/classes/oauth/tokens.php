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
 * Grants and tokens: issuing them at the token endpoint, and checking access tokens.
 *
 * A grant is one user's approval of one client. Revoking it deletes every token issued under it.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tokens {
    /** @var int Access token lifetime in seconds (spec: at most 1 hour). */
    public const ACCESS_TTL = HOURSECS;

    /** @var int Refresh token lifetime in seconds (spec: at most 30 days). */
    public const REFRESH_TTL = 30 * DAYSECS;

    /** @var int The grant's last-use time is written at most this often, in seconds. */
    private const LASTUSED_RESOLUTION = 60;

    /**
     * Handles a token request.
     *
     * @param array $params form parameters of the POST body
     * @param array|null $basic client ID and secret from HTTP Basic authentication
     * @return array{0: int, 1: array} HTTP status and response document
     */
    public static function handle(array $params, ?array $basic): array {
        $client = self::authenticate_client($params, $basic);
        if (is_array($client)) {
            return $client;
        }
        $grant = $params['grant_type'] ?? '';
        $secret = (string) ($grant === 'refresh_token' ? ($params['refresh_token'] ?? '') : ($params['code'] ?? ''));
        // Codes and refresh tokens are single use: claim each under a lock, so two parallel requests with the
        // same value cannot both succeed.
        $lock = $secret === '' ? null : \core\lock\lock_config::get_lock_factory('local_nitro')
            ->get_lock('token_' . hash('sha256', $secret), 5);
        if ($secret !== '' && !$lock) {
            return self::error(400, 'invalid_grant', 'The code or token is being used by another request.');
        }
        try {
            return match ($grant) {
                'authorization_code' => self::exchange_code($params, $client),
                'refresh_token' => self::refresh($params, $client),
                default => self::error(400, 'unsupported_grant_type', 'Use authorization_code or refresh_token.'),
            };
        } finally {
            $lock?->release();
        }
    }

    /**
     * Identifies and authenticates the client; returns the client or an error response.
     *
     * @param array $params
     * @param array|null $basic
     * @return client|array
     */
    private static function authenticate_client(array $params, ?array $basic): client|array {
        [$clientid, $secret] = $basic ?? [$params['client_id'] ?? '', $params['client_secret'] ?? null];
        if ($basic !== null && isset($params['client_id']) && $params['client_id'] !== $clientid) {
            return self::error(400, 'invalid_request', 'client_id differs from the authenticated client.');
        }
        if ($basic !== null && isset($params['client_secret'])) {
            return self::error(400, 'invalid_request', 'Use one client authentication method, not two (RFC 6749, 2.3).');
        }
        $client = $clientid === '' ? null : self::client_for_token_request($clientid);
        if ($client === null) {
            return self::error(401, 'invalid_client', 'Unknown client.');
        }
        if ($client->is_confidential() && ($secret === null || $secret === '' || !clients::verify_secret($client, $secret))) {
            return self::error(401, 'invalid_client', 'This client must authenticate with its client secret.');
        }
        return $client;
    }

    /**
     * The client of a token request.
     *
     * A client whose ID is a metadata document URL is accepted without fetching or caching that document
     * once it holds an authorization code or a connection: its identity was checked at the authorization
     * endpoint, it can hold no secret, and the code or refresh token is what proves this request. Fetching
     * here would make an unauthenticated endpoint send outbound requests, and relying on the cache alone
     * breaks every refresh after a cache purge.
     *
     * @param string $clientid
     * @return client|null
     */
    private static function client_for_token_request(string $clientid): ?client {
        global $DB;
        if (!cimd::looks_like_url($clientid)) {
            return clients::find($clientid);
        }
        $known = $DB->get_field('local_nitro_grant', 'clientname', ['clientid' => $clientid], IGNORE_MULTIPLE);
        if ($known === false) {
            $known = $DB->get_field('local_nitro_code', 'clientname', ['clientid' => $clientid], IGNORE_MULTIPLE);
        }
        if ($known === false) {
            // Not seen before: only the cache may answer, never a fetch.
            return cimd::resolve($clientid, false);
        }
        return new client(
            clientid: $clientid,
            name: (string) $known,
            redirecturis: [],
            authmethod: 'none',
            origin: 'cimd',
        );
    }

    /**
     * Exchanges an authorization code.
     *
     * @param array $params
     * @param client $client
     * @return array
     */
    private static function exchange_code(array $params, client $client): array {
        global $DB;
        $code = (string) ($params['code'] ?? '');
        $row = $code === '' ? false : $DB->get_record('local_nitro_code', ['codehash' => hash('sha256', $code)]);
        if ($row) {
            // Single use: gone as soon as it is presented, whatever happens next.
            $DB->delete_records('local_nitro_code', ['id' => $row->id]);
        }
        if (!$row || $row->expires < time()) {
            return self::error(400, 'invalid_grant', 'The authorization code is invalid, used or expired.');
        }
        if ($row->clientid !== $client->clientid || ($params['redirect_uri'] ?? null) !== $row->redirecturi) {
            return self::error(400, 'invalid_grant', 'client_id or redirect_uri does not match the authorization request.');
        }
        $verifier = (string) ($params['code_verifier'] ?? '');
        if (!preg_match('/^[A-Za-z0-9._~-]{43,128}$/', $verifier) || !hash_equals($row->codechallenge, self::s256($verifier))) {
            return self::error(400, 'invalid_grant', 'code_verifier does not match the code_challenge.');
        }
        $resource = $params['resource'] ?? null;
        if ($resource !== null && $resource !== '' && $resource !== metadata::resource()) {
            return self::error(400, 'invalid_target', 'The resource must be ' . metadata::resource() . '.');
        }
        if (!self::user_is_active((int) $row->userid) || !authorization::user_may_connect((int) $row->userid)) {
            return self::error(400, 'invalid_grant', 'The user can no longer connect AI clients.');
        }

        $grantid = self::save_grant((int) $row->userid, $client, $row);
        clients::mark_used($client->clientid);
        return [200, self::issue($grantid, $row->scope)];
    }

    /**
     * Rotates a refresh token.
     *
     * @param array $params
     * @param client $client
     * @return array
     */
    private static function refresh(array $params, client $client): array {
        global $DB;
        $token = (string) ($params['refresh_token'] ?? '');
        $row = $token === '' ? false : $DB->get_record(
            'local_nitro_token',
            ['tokenhash' => hash('sha256', $token), 'tokentype' => 'refresh']
        );
        if (!$row) {
            return self::error(400, 'invalid_grant', 'Unknown refresh token.');
        }
        $grant = $DB->get_record('local_nitro_grant', ['id' => $row->grantid]);
        if (!$grant) {
            return self::error(400, 'invalid_grant', 'The connection was revoked.');
        }
        if ($row->used) {
            // A rotated token came back: someone else has a copy. End the whole connection.
            self::revoke_grant($grant->id);
            return self::error(400, 'invalid_grant', 'This refresh token was already used; the connection was revoked.');
        }
        if ($row->expires < time()) {
            return self::error(400, 'invalid_grant', 'The refresh token has expired.');
        }
        if ($grant->clientid !== $client->clientid) {
            return self::error(400, 'invalid_grant', 'This refresh token belongs to another client.');
        }
        if (!self::user_is_active((int) $grant->userid) || !authorization::user_may_connect((int) $grant->userid)) {
            self::revoke_grant($grant->id);
            return self::error(400, 'invalid_grant', 'The user can no longer connect AI clients.');
        }
        $DB->set_field('local_nitro_token', 'used', 1, ['id' => $row->id]);
        return [200, self::issue($grant->id, $grant->scope)];
    }

    /**
     * Creates the grant for a user and client, or refreshes the existing one.
     *
     * @param int $userid
     * @param client $client
     * @param \stdClass $code the redeemed code row
     * @return int grant ID
     */
    private static function save_grant(int $userid, client $client, \stdClass $code): int {
        global $DB;
        $existing = $DB->get_record('local_nitro_grant', ['userid' => $userid, 'clientid' => $client->clientid]);
        $record = [
            'userid' => $userid,
            'clientid' => $client->clientid,
            'clientname' => $code->clientname ?: $client->name,
            'redirecthost' => redirect_uris::host($code->redirecturi),
            'scope' => $code->scope,
            'timecreated' => time(),
        ];
        if ($existing) {
            $DB->update_record('local_nitro_grant', ['id' => $existing->id] + $record);
            return (int) $existing->id;
        }
        return (int) $DB->insert_record('local_nitro_grant', $record);
    }

    /**
     * Issues an access token, and a refresh token when offline access was granted.
     *
     * @param int $grantid
     * @param string $scope
     * @return array token response
     */
    private static function issue(int $grantid, string $scope): array {
        $response = [
            'access_token' => self::store($grantid, 'access', self::ACCESS_TTL),
            'token_type' => 'Bearer',
            'expires_in' => self::ACCESS_TTL,
            'scope' => $scope,
        ];
        if (in_array('offline_access', explode(' ', $scope), true)) {
            $response['refresh_token'] = self::store($grantid, 'refresh', self::REFRESH_TTL);
        }
        return $response;
    }

    /**
     * Stores the hash of a new token and returns the token.
     *
     * @param int $grantid
     * @param string $type access or refresh
     * @param int $ttl
     * @return string
     */
    private static function store(int $grantid, string $type, int $ttl): string {
        global $DB;
        $token = clients::random_token();
        $DB->insert_record('local_nitro_token', [
            'grantid' => $grantid,
            'tokentype' => $type,
            'tokenhash' => hash('sha256', $token),
            'used' => 0,
            'expires' => time() + $ttl,
            'timecreated' => time(),
        ]);
        return $token;
    }

    /**
     * Checks an access token presented to the MCP endpoint.
     *
     * @param string $token
     * @return \stdClass|null the grant (userid, clientid, scope, ...) or null if the token must be refused
     */
    public static function validate_access(string $token): ?\stdClass {
        global $DB;
        if (!\local_nitro\local\plugin::active() || $token === '') {
            return null;
        }
        $row = $DB->get_record('local_nitro_token', ['tokenhash' => hash('sha256', $token), 'tokentype' => 'access']);
        if (!$row || $row->expires < time()) {
            return null;
        }
        $grant = $DB->get_record('local_nitro_grant', ['id' => $row->grantid]);
        if (!$grant || !self::user_is_active((int) $grant->userid)) {
            return null;
        }
        if ($grant->timelastused < time() - self::LASTUSED_RESOLUTION) {
            $DB->set_field('local_nitro_grant', 'timelastused', time(), ['id' => $grant->id]);
        }
        return $grant;
    }

    /**
     * Revokes a grant: deletes it and every token issued under it.
     *
     * @param int $grantid
     */
    public static function revoke_grant(int $grantid): void {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('local_nitro_token', ['grantid' => $grantid]);
        $DB->delete_records('local_nitro_grant', ['id' => $grantid]);
        $transaction->allow_commit();
    }

    /**
     * Whether a user may still act: exists, not deleted, not suspended, can log in.
     *
     * @param int $userid
     * @return bool
     */
    public static function user_is_active(int $userid): bool {
        global $DB;
        $user = $DB->get_record('user', ['id' => $userid], 'id, deleted, suspended, auth');
        return $user && !$user->deleted && !$user->suspended && $user->auth !== 'nologin'
            && is_enabled_auth($user->auth);
    }

    /**
     * The PKCE S256 transform of a code verifier.
     *
     * @param string $verifier
     * @return string
     */
    public static function s256(string $verifier): string {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    /**
     * An RFC 6749 error response.
     *
     * @param int $status
     * @param string $error
     * @param string $description
     * @return array
     */
    private static function error(int $status, string $error, string $description): array {
        return [$status, ['error' => $error, 'error_description' => $description]];
    }
}
