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
 * Date handling for tools: ISO 8601 in, ISO 8601 with offset and weekday out, in the user's time zone.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates {
    /**
     * A timestamp as ISO 8601 in the current user's time zone; null for 0 (not set).
     *
     * @param int|null $timestamp
     * @return string|null
     */
    public static function iso(?int $timestamp): ?string {
        if (empty($timestamp)) {
            return null;
        }
        return (new \DateTimeImmutable('@' . $timestamp))
            ->setTimezone(\core_date::get_user_timezone_object())
            ->format(DATE_ATOM);
    }

    /**
     * Parses an ISO 8601 date-time; without an offset it is taken in the user's Moodle time zone.
     *
     * @param string $value for example 2026-10-09T23:59 or 2026-10-09T23:59:00+02:00
     * @param string $param parameter name, for the error message
     * @return int timestamp
     * @throws \invalid_parameter_exception
     */
    public static function parse(string $value, string $param): int {
        $value = str_replace(' ', 'T', trim($value));
        $pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?(\.\d+)?(Z|[+-]\d{2}:?\d{2})?$/';
        try {
            if (!preg_match($pattern, $value)) {
                throw new \Exception('format');
            }
            // Without an offset the user's time zone applies; with one, the offset wins.
            $date = new \DateTimeImmutable($value, \core_date::get_user_timezone_object());
            if ($date->format('Y-m-d') !== substr($value, 0, 10)) {
                // PHP rolls 2026-02-30 over to March; refuse instead.
                throw new \Exception('no such day');
            }
            return $date->getTimestamp();
        } catch (\Exception $e) {
            throw new \invalid_parameter_exception("{$param}: '{$value}' is not an ISO 8601 date and time. "
                . "Use for example 2026-10-09T23:59 (taken in the teacher's time zone) or 2026-10-09T23:59:00+02:00.");
        }
    }

    /**
     * A timestamp for tool results: ISO 8601 with offset, plus the weekday in the user's language.
     *
     * @param int|null $timestamp
     * @return array{date: ?string, weekday: ?string}
     */
    public static function describe(?int $timestamp): array {
        if (empty($timestamp)) {
            return ['date' => null, 'weekday' => null];
        }
        return ['date' => self::iso($timestamp), 'weekday' => userdate($timestamp, '%A')];
    }

    /**
     * Return structure for describe().
     *
     * @param string $desc
     * @return \core_external\external_single_structure
     */
    public static function describe_returns(string $desc): \core_external\external_single_structure {
        return new \core_external\external_single_structure([
            'date' => new \core_external\external_value(
                PARAM_RAW,
                'ISO 8601 with offset; null if not set',
                VALUE_REQUIRED,
                null,
                NULL_ALLOWED
            ),
            'weekday' => new \core_external\external_value(
                PARAM_TEXT,
                'Weekday, to read back to the teacher',
                VALUE_REQUIRED,
                null,
                NULL_ALLOWED
            ),
        ], $desc);
    }
}
