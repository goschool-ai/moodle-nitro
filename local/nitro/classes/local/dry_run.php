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
 * Dry runs for write tools: the write runs inside a database transaction that is always rolled
 * back, so the result shows exactly what Moodle would store (defaults, cleaning, derived settings)
 * and nothing persists. Messages and non-internal event observers are deferred by Moodle until
 * commit, so the rollback discards them too.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dry_run {
    /**
     * The dry_run parameter every write tool accepts.
     *
     * @return external_value
     */
    public static function param(): external_value {
        return new external_value(PARAM_BOOL, 'Preview only: report what would change, including the effective '
            . 'settings, and change nothing', VALUE_DEFAULT, false);
    }

    /**
     * The note every dry run carries, so nobody builds on numbers that were never kept.
     *
     * @return external_value
     */
    public static function note_returns(): external_value {
        return new external_value(PARAM_TEXT, 'On a dry run: what the preview does and does not say; '
            . 'empty after a real run');
    }

    /**
     * Runs a write, for real or as a dry run.
     *
     * @param bool $dryrun
     * @param int $courseid course whose caches must not keep dry-run state
     * @param callable $write receives whether this is a dry run and returns the tool result; on a dry
     *                        run it MUST leave the IDs and URLs of records it would create empty,
     *                        because the rollback throws those records away
     * @return array the tool result, with `dry_run` and `dry_run_note` set
     */
    public static function run(bool $dryrun, int $courseid, callable $write): array {
        global $DB;
        if (!$dryrun) {
            $result = $write(false);
            $result['dry_run'] = false;
            $result['dry_run_note'] = '';
            return $result;
        }
        $transaction = $DB->start_delegated_transaction();
        try {
            $result = $write(true);
            throw new dry_run_rollback($result);
        } catch (\Throwable $e) {
            try {
                $transaction->rollback($e);
            } catch (dry_run_rollback $done) {
                self::forget_course_state($courseid);
                $result = $done->result;
                $result['dry_run'] = true;
                $result['dry_run_note'] = 'Nothing was saved: the write ran inside a transaction that was '
                    . 'rolled back. The settings above are what Moodle would store. Records that would be '
                    . 'created have no ID or URL here yet; run again without dry_run to create them.';
                return $result;
            }
        }
        // Not reached: rollback() rethrows every other exception.
        throw new \coding_exception('Dry run did not roll back.');
    }

    /**
     * Drops caches that may hold state from the rolled-back write.
     *
     * @param int $courseid
     */
    private static function forget_course_state(int $courseid): void {
        // Delete the entry rather than bumping cacherev: the rolled-back write may have cached its state
        // under exactly the revision a bump in the same second would produce, and a bump is a write.
        $cache = \cache::make('core', 'coursemodinfo');
        $cache->acquire_lock($courseid);
        try {
            $cache->delete($courseid);
        } finally {
            $cache->release_lock($courseid);
        }
        get_fast_modinfo(0, 0, true);
    }
}
