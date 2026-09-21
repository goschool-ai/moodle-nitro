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

use core_external\external_value;

/**
 * Confirmation tokens for writes that reach students: the first call returns a preview and a token;
 * the write happens only when the same user calls the same tool again with the same arguments and
 * that token, within 10 minutes, once.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirmation {
    /** @var int Token lifetime in seconds (spec: at most 10 minutes). */
    public const TTL = 600;

    /** @var string[] Arguments left out of the hash: they differ between preview and confirmation. */
    private const IGNORED = ['confirmation_token', 'dry_run'];

    /**
     * The confirmation_token parameter.
     *
     * @return external_value
     */
    public static function param(): external_value {
        return new external_value(PARAM_ALPHANUMEXT, 'Leave empty on the first call, which only returns a preview. '
            . 'After the teacher explicitly approves the preview, call again with exactly the same arguments and the '
            . 'confirmation_token from the preview.', VALUE_DEFAULT, '');
    }

    /**
     * Hash of the arguments that must not change between preview and confirmation.
     *
     * @param array $args
     * @return string
     */
    public static function args_hash(array $args): string {
        foreach (self::IGNORED as $key) {
            unset($args[$key]);
        }
        return hash('sha256', json_encode(self::canonical($args), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Issues a token for a previewed call.
     *
     * @param string $tool
     * @param array $args
     * @return array{token: string, expires: string} the token and its expiry (ISO 8601)
     */
    public static function issue(string $tool, array $args): array {
        global $DB, $USER;
        $token = \local_nitro\oauth\clients::random_token();
        $expires = time() + self::TTL;
        $DB->insert_record('local_nitro_confirm', [
            'userid' => $USER->id,
            'tool' => $tool,
            'argshash' => self::args_hash($args),
            'tokenhash' => hash('sha256', $token),
            'used' => 0,
            'expires' => $expires,
            'timecreated' => time(),
        ]);
        return ['token' => $token, 'expires' => dates::iso($expires)];
    }

    /**
     * Uses up a token; throws if it does not confirm exactly this call.
     *
     * @param string $tool
     * @param array $args including confirmation_token
     * @throws \moodle_exception asking for a new preview
     */
    public static function redeem(string $tool, array $args): void {
        global $DB, $USER;
        $token = (string) ($args['confirmation_token'] ?? '');
        // Single use even with parallel calls: hold a lock on the token while checking and using it up.
        $lock = $token === '' ? null : \core\lock\lock_config::get_lock_factory('local_nitro')
            ->get_lock('confirm_' . hash('sha256', $token), 5);
        if ($token !== '' && !$lock) {
            throw new \moodle_exception('confirmused', 'local_nitro');
        }
        try {
            self::claim($tool, $args, $token);
        } finally {
            $lock?->release();
        }
    }

    /**
     * Checks and uses up a token; called under the token's lock.
     *
     * @param string $tool
     * @param array $args
     * @param string $token
     */
    private static function claim(string $tool, array $args, string $token): void {
        global $DB, $USER;
        $row = $token === '' ? false : $DB->get_record('local_nitro_confirm', ['tokenhash' => hash('sha256', $token)]);
        $problem = match (true) {
            !$row, (int) $row->userid !== (int) $USER->id => 'confirmunknown',
            (bool) $row->used => 'confirmused',
            $row->expires < time() => 'confirmexpired',
            $row->tool !== $tool, !hash_equals($row->argshash, self::args_hash($args)) => 'confirmchanged',
            default => null,
        };
        if ($problem !== null) {
            throw new \moodle_exception($problem, 'local_nitro');
        }
        $DB->set_field('local_nitro_confirm', 'used', 1, ['id' => $row->id]);
    }

    /**
     * Arguments with keys sorted at every level, so equal calls hash equally.
     *
     * @param mixed $value
     * @return mixed
     */
    private static function canonical(mixed $value): mixed {
        if (!is_array($value)) {
            return $value;
        }
        if (!array_is_list($value)) {
            ksort($value);
        }
        return array_map([self::class, 'canonical'], $value);
    }

    /**
     * Fields every confirmation tool returns about the protocol.
     *
     * @param array|null $issued from issue() on a preview; null once executed
     * @return array
     */
    public static function result_fields(?array $issued): array {
        if ($issued === null) {
            return ['executed' => true, 'confirmation_token' => '', 'confirmation_expires' => null, 'instruction' => ''];
        }
        return [
            'executed' => false,
            'confirmation_token' => $issued['token'],
            'confirmation_expires' => $issued['expires'],
            'instruction' => 'Nothing has happened yet. Show this preview to the teacher word for word and ask for '
                . 'explicit approval. Only if they approve, call the tool again with exactly the same arguments and this '
                . 'confirmation_token. If they want any change, call it again without a token for a new preview.',
        ];
    }

    /**
     * Return structure for result_fields().
     *
     * @return array
     */
    public static function result_returns(): array {
        return [
            'executed' => new \core_external\external_value(PARAM_BOOL, 'True if the action happened; false for a preview'),
            'confirmation_token' => new \core_external\external_value(PARAM_RAW, 'Token to confirm this preview; empty '
                . 'once executed'),
            'confirmation_expires' => new \core_external\external_value(
                PARAM_RAW,
                'When the token expires, ISO 8601',
                VALUE_REQUIRED,
                null,
                NULL_ALLOWED
            ),
            'instruction' => new \core_external\external_value(PARAM_RAW, 'What to do with the preview'),
        ];
    }
}
