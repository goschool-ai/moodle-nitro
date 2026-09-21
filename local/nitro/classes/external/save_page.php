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
use local_nitro\local\content;
use local_nitro\local\dry_run;
use local_nitro\local\modules;

/**
 * Tool save_page: create or update a Page by its key, from markdown.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_page extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'key' => new external_value(PARAM_RAW, 'Stable key of the page in this course, for example '
                . 'week4-requirements. The same key updates the same page.'),
            'name' => new external_value(
                PARAM_TEXT,
                'Title. Required when creating; on update, omit to keep it.',
                VALUE_DEFAULT,
                null
            ),
            'content' => new external_value(PARAM_RAW, 'Page content in markdown. Required when creating; on update, '
                . 'omit to keep it.', VALUE_DEFAULT, null),
            'section' => new external_value(PARAM_INT, 'Section number (0 is the general section). Default 0 when '
                . 'creating; on update, omit to keep it.', VALUE_DEFAULT, null),
            'visible' => new external_value(PARAM_BOOL, 'Visible to students. Default true when creating; on update, '
                . 'omit to keep it.', VALUE_DEFAULT, null),
            'dry_run' => dry_run::param(),
        ]);
    }

    /**
     * Creates or updates the page.
     *
     * @param int $courseid
     * @param string $key
     * @param string|null $name
     * @param string|null $content
     * @param int|null $section
     * @param bool|null $visible
     * @param bool $dryrun
     * @return array
     */
    public static function execute(
        int $courseid,
        string $key,
        ?string $name = null,
        ?string $content = null,
        ?int $section = null,
        ?bool $visible = null,
        bool $dryrun = false
    ): array {
        global $CFG;
        require_once($CFG->libdir . '/resourcelib.php');
        // Omitted (null) arguments are left out, so their defaults apply as in an MCP call.
        $params = self::validate_parameters(self::execute_parameters(), array_filter([
            'courseid' => $courseid, 'key' => $key, 'name' => $name, 'content' => $content,
            'section' => $section, 'visible' => $visible, 'dry_run' => $dryrun,
        ], fn($value) => $value !== null));
        $context = access::require_course($params['courseid']);
        require_capability('moodle/course:manageactivities', $context);
        $key = modules::check_key($params['key']);
        if ($params['section'] !== null) {
            modules::check_section($params['courseid'], $params['section']);
        }
        $converted = $params['content'] === null ? null : content::from_markdown($params['content']);

        $existing = modules::find_by_key($params['courseid'], $key);
        if ($existing && $existing->modname !== 'page') {
            throw new \invalid_parameter_exception("The key '{$key}' belongs to a {$existing->modname} activity, not a page. "
                . 'Use another key.');
        }
        if (!$existing && ($params['name'] === null || $converted === null)) {
            throw new \invalid_parameter_exception("No page with the key '{$key}' exists yet, so this creates one: "
                . 'name and content are required.');
        }

        $write = function (bool $dryrun) use ($params, $key, $converted, $existing) {
            global $DB;
            if ($existing) {
                $before = $DB->get_record('page', ['id' => $existing->instance], '*', MUST_EXIST);
                $data = modules::current_data($existing);
                if ($params['name'] !== null) {
                    $data->name = $params['name'];
                }
                if ($converted !== null) {
                    $data->page['text'] = $converted['html'];
                    $data->page['format'] = FORMAT_HTML;
                }
                if ($params['section'] !== null) {
                    $data->section = $params['section'];
                }
                if ($params['visible'] !== null) {
                    $data->visible = (int) $params['visible'];
                }
                modules::update($existing, $data);
                if ($params['section'] !== null && $params['section'] != $existing->sectionnum) {
                    moveto_module($existing, get_fast_modinfo($params['courseid'])->get_section_info($params['section']));
                }
                $status = 'updated';
                $cmid = $existing->id;
            } else {
                $before = null;
                $cm = modules::create((object) [
                    'modulename' => 'page',
                    'course' => $params['courseid'],
                    'section' => $params['section'] ?? 0,
                    'visible' => (int) ($params['visible'] ?? true),
                    'cmidnumber' => $key,
                    'name' => $params['name'],
                    'introeditor' => ['text' => '', 'format' => FORMAT_HTML, 'itemid' => 0],
                    // Without a form, page_add_instance() reads content directly; updates read the editor array.
                    'content' => $converted['html'],
                    'contentformat' => FORMAT_HTML,
                    'page' => ['text' => $converted['html'], 'format' => FORMAT_HTML, 'itemid' => 0],
                    'display' => RESOURCELIB_DISPLAY_AUTO,
                    'printintro' => 0,
                    'printlastmodified' => 1,
                ]);
                $status = 'created';
                $cmid = $cm->id;
            }
            $cm = get_fast_modinfo($params['courseid'])->get_cm($cmid);
            $after = $DB->get_record('page', ['id' => $cm->instance], '*', MUST_EXIST);
            $changed = $before === null ? [] : modules::changed($before, $after, ['name', 'content']);
            if ($before !== null && $params['visible'] !== null && (int) $existing->visible !== (int) $cm->visible) {
                $changed[] = 'visible';
            }
            if ($before !== null && (int) $existing->sectionnum !== (int) $cm->sectionnum) {
                $changed[] = 'section';
            }
            $fabricated = $dryrun && $status === 'created';
            return [
                'status' => $status,
                'cmid' => $fabricated ? 0 : (int) $cm->id,
                'key' => $key,
                'url' => $fabricated ? '' : $cm->url->out(false),
                'name' => $cm->get_formatted_name(),
                'section' => (int) $cm->sectionnum,
                'visible' => (bool) $cm->visible,
                'changed' => $changed,
                'removed_by_cleaning' => $converted['removed'] ?? [],
            ];
        };
        return dry_run::run($params['dry_run'], $params['courseid'], $write);
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'created or updated (with dry_run: would be)'),
            'dry_run' => new external_value(PARAM_BOOL, 'True if nothing was changed'),
            'dry_run_note' => dry_run::note_returns(),
            'cmid' => new external_value(PARAM_INT, 'Course module ID; 0 when a dry run would create the '
                . 'page, because it was not created'),
            'key' => new external_value(PARAM_RAW, 'Key'),
            'url' => new external_value(PARAM_URL, 'Page URL'),
            'name' => new external_value(PARAM_TEXT, 'Title'),
            'section' => new external_value(PARAM_INT, 'Section number'),
            'visible' => new external_value(PARAM_BOOL, 'Visible to students'),
            'changed' => new external_multiple_structure(
                new external_value(PARAM_ALPHANUMEXT, 'Field'),
                'Fields that changed on update'
            ),
            'removed_by_cleaning' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'Removed item'),
                'Elements and attributes Moodle\'s HTML cleaning removed from the content; tell the teacher'
            ),
        ]);
    }
}
