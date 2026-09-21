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
 * Registered OAuth clients: lookup, dynamic and admin registration, deletion.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clients {
    /** @var string[] Token endpoint authentication methods nitro supports. */
    public const AUTH_METHODS = ['none', 'client_secret_post', 'client_secret_basic'];

    /** @var int Dynamically registered clients without a token are deleted after this many seconds. */
    public const UNUSED_LIFETIME = 7 * DAYSECS;

    /**
     * Finds a client by ID: a CIMD URL is resolved from its document, anything else from the table.
     *
     * @param string $clientid
     * @param bool $fetch whether a CIMD document may be fetched (false: cache only)
     * @return client|null
     */
    public static function find(string $clientid, bool $fetch = true): ?client {
        global $DB;
        if (cimd::looks_like_url($clientid)) {
            return cimd::resolve($clientid, $fetch);
        }
        $record = $DB->get_record('local_nitro_client', ['clientid' => $clientid]);
        return $record ? self::from_record($record) : null;
    }

    /**
     * Registers a client and returns it with its plain secret (null for public clients).
     *
     * @param string $origin dcr or admin
     * @param string $name
     * @param string[] $redirecturis must all be on the allowlist
     * @param string $authmethod one of AUTH_METHODS
     * @param int|null $createdby admin user ID for admin registrations
     * @return array{0: client, 1: ?string}
     */
    public static function register(
        string $origin,
        string $name,
        array $redirecturis,
        string $authmethod,
        ?int $createdby = null
    ): array {
        global $DB;
        if (!in_array($authmethod, self::AUTH_METHODS, true)) {
            throw new \coding_exception("Unsupported auth method {$authmethod}");
        }
        foreach ($redirecturis as $uri) {
            if (!redirect_uris::is_allowed($uri)) {
                throw new \coding_exception("Redirect URI not on the allowlist: {$uri}");
            }
        }
        $secret = $authmethod === 'none' ? null : self::random_token();
        $now = time();
        $record = (object) [
            'clientid' => bin2hex(random_bytes(16)),
            'name' => \core_text::substr($name, 0, 255),
            'secrethash' => $secret === null ? null : hash('sha256', $secret),
            'authmethod' => $authmethod,
            'redirecturis' => json_encode(array_values($redirecturis), JSON_UNESCAPED_SLASHES),
            'origin' => $origin,
            'createdby' => $createdby,
            'timefirstused' => null,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $record->id = $DB->insert_record('local_nitro_client', $record);
        return [self::from_record($record), $secret];
    }

    /**
     * Whether a presented secret is the client's secret.
     *
     * @param client $client
     * @param string $secret
     * @return bool
     */
    public static function verify_secret(client $client, string $secret): bool {
        return $client->secrethash !== null && hash_equals($client->secrethash, hash('sha256', $secret));
    }

    /**
     * Records that a client obtained a token, so the clean-up keeps it.
     *
     * @param string $clientid
     */
    public static function mark_used(string $clientid): void {
        global $DB;
        $DB->set_field_select(
            'local_nitro_client',
            'timefirstused',
            time(),
            'clientid = ? AND timefirstused IS NULL',
            [$clientid]
        );
    }

    /**
     * Number of dynamically registered clients that never obtained a token.
     *
     * @return int
     */
    public static function count_never_used(): int {
        global $DB;
        return $DB->count_records_select('local_nitro_client', "origin = 'dcr' AND timefirstused IS NULL");
    }

    /**
     * Whether the never-used client cap is reached.
     *
     * @return bool
     */
    public static function cap_reached(): bool {
        $cap = (int) get_config('local_nitro', 'dcrcap');
        return $cap > 0 && self::count_never_used() >= $cap;
    }

    /**
     * Deletes a client together with everything issued to it.
     *
     * @param string $clientid
     */
    public static function delete(string $clientid): void {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $grantids = $DB->get_fieldset('local_nitro_grant', 'id', ['clientid' => $clientid]);
        if ($grantids) {
            [$insql, $params] = $DB->get_in_or_equal($grantids);
            $DB->delete_records_select('local_nitro_token', "grantid {$insql}", $params);
            $DB->delete_records_list('local_nitro_grant', 'id', $grantids);
        }
        $DB->delete_records('local_nitro_code', ['clientid' => $clientid]);
        $DB->delete_records('local_nitro_client', ['clientid' => $clientid]);
        $transaction->allow_commit();
    }

    /**
     * Deletes dynamically registered clients that obtained no token within UNUSED_LIFETIME.
     *
     * @return int number of clients deleted
     */
    public static function delete_unused(): int {
        global $DB;
        $ids = $DB->get_fieldset_select(
            'local_nitro_client',
            'clientid',
            "origin = 'dcr' AND timefirstused IS NULL AND timecreated < ?",
            [time() - self::UNUSED_LIFETIME]
        );
        foreach ($ids as $clientid) {
            self::delete($clientid);
        }
        return count($ids);
    }

    /**
     * A random 256-bit value, as used for secrets and tokens.
     *
     * @return string
     */
    public static function random_token(): string {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * Builds a client from a table row.
     *
     * @param \stdClass $record
     * @return client
     */
    private static function from_record(\stdClass $record): client {
        return new client(
            clientid: $record->clientid,
            name: (string) $record->name,
            redirecturis: json_decode($record->redirecturis, true) ?: [],
            authmethod: $record->authmethod,
            origin: $record->origin,
            secrethash: $record->secrethash,
        );
    }
}
