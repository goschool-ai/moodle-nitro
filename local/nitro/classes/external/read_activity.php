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

namespace local_nitro\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_nitro\local\access;
use local_nitro\local\dates;

/**
 * Tool read_activity: the text of one activity, so the AI can work from the course's own material.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_activity extends external_api {
    /** @var int Longest text returned per field, in characters. */
    private const MAX_TEXT = 20000;

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID of the activity, from course_overview'),
            'include_html' => new external_value(PARAM_BOOL, 'Also return the HTML, not only the plain text; only '
                . 'needed when republishing the same content', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Reads the activity.
     *
     * @param int $cmid
     * @param bool $includehtml
     * @return array
     */
    public static function execute(int $cmid, bool $includehtml = false): array {
        global $DB, $USER;
        $params = self::validate_parameters(
            self::execute_parameters(),
            ['cmid' => $cmid, 'include_html' => $includehtml]
        );
        $context = access::require_module($params['cmid']);
        [$course, $cm] = get_course_and_cm_from_cmid($params['cmid']);
        if (!$cm->uservisible && !has_capability('moodle/course:viewhiddenactivities', $context)) {
            throw new \required_capability_exception($context, 'moodle/course:viewhiddenactivities', 'nopermissions', '');
        }
        $instance = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);

        // The description every activity may have, and the body of the ones that keep their text in the course.
        $body = match ($cm->modname) {
            'page', 'book' => ['content', 'contentformat'],
            'label' => [null, null],
            default => [null, null],
        };
        $result = [
            'cmid' => (int) $cm->id,
            'key' => (string) $cm->idnumber,
            'type' => $cm->modname,
            'name' => $cm->get_formatted_name(),
            'visible' => (bool) $cm->visible,
            'url' => $cm->url ? $cm->url->out(false) : '',
            'description' => self::text($instance->intro ?? '', (int) ($instance->introformat ?? FORMAT_HTML), $context),
            'content' => $body[0] === null ? '' : self::text(
                (string) ($instance->{$body[0]} ?? ''),
                (int) ($instance->{$body[1]} ?? FORMAT_HTML),
                $context
            ),
            'link' => $cm->modname === 'url' ? (string) ($instance->externalurl ?? '') : '',
            'dates' => array_map(fn($date) => [
                'type' => (string) ($date['dataid'] ?? ''),
                'label' => (string) $date['label'],
                'date' => dates::iso((int) $date['timestamp']),
            ], \core\activity_dates::get_dates_for_module($cm, $USER->id)),
            'note' => '',
        ];
        if ($result['description'] === '' && $result['content'] === '') {
            $result['note'] = "This {$cm->modname} keeps no text in Moodle that nitro can read (for example an "
                . 'uploaded file). Ask the teacher for the material, or use another activity.';
        }
        if ($params['include_html']) {
            $result['description_html'] = self::html((string) ($instance->intro ?? ''), $context);
            $result['content_html'] = $body[0] === null ? '' : self::html((string) ($instance->{$body[0]} ?? ''), $context);
        }
        return $result;
    }

    /**
     * Stored text as plain text, as a reader would see it.
     *
     * @param string $text
     * @param int $format
     * @param \context $context
     * @return string
     */
    private static function text(string $text, int $format, \context $context): string {
        if (trim($text) === '') {
            return '';
        }
        $html = format_text($text, $format, ['context' => $context, 'para' => false]);
        return \core_text::substr(trim(html_to_text($html, 0, false)), 0, self::MAX_TEXT);
    }

    /**
     * Stored text as HTML, with file URLs left as Moodle stores them.
     *
     * @param string $text
     * @param \context $context
     * @return string
     */
    private static function html(string $text, \context $context): string {
        return \core_text::substr(trim($text), 0, self::MAX_TEXT);
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
            'key' => new external_value(PARAM_RAW, 'Stable key (ID number); empty if none'),
            'type' => new external_value(PARAM_PLUGIN, 'Activity type'),
            'name' => new external_value(PARAM_TEXT, 'Name'),
            'visible' => new external_value(PARAM_BOOL, 'Visible to students'),
            'url' => new external_value(PARAM_RAW, 'Activity URL'),
            'description' => new external_value(PARAM_RAW, 'Description (intro) as plain text'),
            'content' => new external_value(PARAM_RAW, 'The body as plain text, for activities that have one '
                . '(a page, a book); empty otherwise'),
            'link' => new external_value(PARAM_RAW, 'Target address for a URL activity; empty otherwise'),
            'dates' => new external_multiple_structure(new external_single_structure([
                'type' => new external_value(PARAM_ALPHANUMEXT, 'Date kind'),
                'label' => new external_value(PARAM_TEXT, 'Label as shown in Moodle'),
                'date' => new external_value(PARAM_RAW, 'ISO 8601'),
            ])),
            'note' => new external_value(PARAM_RAW, 'Why there is no text, when there is none'),
            'description_html' => new external_value(PARAM_RAW, 'Description as stored HTML', VALUE_OPTIONAL),
            'content_html' => new external_value(PARAM_RAW, 'Body as stored HTML', VALUE_OPTIONAL),
        ]);
    }
}
