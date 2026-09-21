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

namespace local_nitro\mcp;

use core\exception\invalid_parameter_exception;
use core\exception\required_capability_exception;
use core_external\external_api;
use local_nitro\event\tool_called;
use local_nitro\local\access;

/**
 * Runs one tool call as the current user and turns the outcome into an MCP tool result.
 *
 * It follows external_api::call_external_function() (fresh $PAGE and $COURSE, parameter validation,
 * return value cleaning), but keeps the detail that names an invalid parameter, which the core
 * function drops unless developer debugging is on.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dispatcher {
    /**
     * Calls a tool.
     *
     * @param string $tool allowlisted tool name
     * @param \stdClass $info external function info from registry::function_info()
     * @param array $args
     * @param string $clientid OAuth client, for the audit event
     * @return array{isError: bool, data: ?array, message: ?string}
     */
    public static function call(string $tool, \stdClass $info, array $args, string $clientid): array {
        global $PAGE, $COURSE, $SITE, $CFG;
        require_once($CFG->libdir . '/pagelib.php');

        $savedpage = $PAGE;
        $savedcourse = $COURSE;
        $PAGE = new \moodle_page();
        $COURSE = clone($SITE);
        $courseid = isset($args['courseid']) && is_numeric($args['courseid']) ? (int) $args['courseid'] : null;
        try {
            // The per-course gate, before the tool runs; tools check it again themselves.
            if ($courseid !== null) {
                access::require_course($courseid);
            }
            $params = array_values(external_api::validate_parameters($info->parameters_desc, $args));
            $result = call_user_func_array([$info->classname, $info->methodname], $params);
            if ($info->returns_desc !== null) {
                $result = external_api::clean_returnvalue($info->returns_desc, $result);
            }
            $outcome = !empty($result['confirmation_token']) ? 'preview' : 'success';
            $response = ['isError' => false, 'data' => (array) $result, 'message' => null];
        } catch (\Throwable $e) {
            global $DB;
            // A tool that failed inside a transaction must not leave it open: the audit event below and
            // anything after it would be written into it and lost when Moodle aborts it at shutdown.
            if ($DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
            $outcome = 'error';
            $response = ['isError' => true, 'data' => null, 'message' => self::message($e)];
        } finally {
            $PAGE = $savedpage;
            $COURSE = $savedcourse;
        }
        self::log($tool, $clientid, $outcome, $courseid, $response['data']['affected_ids'] ?? []);
        return $response;
    }

    /**
     * A message for the AI that says what went wrong and, where possible, what to change.
     *
     * @param \Throwable $e
     * @return string
     */
    public static function message(\Throwable $e): string {
        if ($e instanceof invalid_parameter_exception) {
            // The debug info names the parameter ("courseid => Invalid parameter value detected").
            $debuginfo = trim((string) ($e->debuginfo ?? ''));
            return str_contains($e->getMessage(), $debuginfo) ? $e->getMessage() : trim($e->getMessage() . ' ' . $debuginfo);
        }
        if ($e instanceof required_capability_exception) {
            return $e->getMessage() . ' The user lacks this Moodle permission in this context; ' .
                'the same action is not allowed in the Moodle web interface either.';
        }
        if ($e instanceof \moodle_exception) {
            return $e->getMessage();
        }
        debugging('nitro tool failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return 'The tool failed with an internal error. Nothing was changed if the error came before saving.';
    }

    /**
     * Triggers the audit event.
     *
     * @param string $tool
     * @param string $clientid
     * @param string $outcome
     * @param int|null $courseid
     * @param array $objectids
     */
    private static function log(string $tool, string $clientid, string $outcome, ?int $courseid, array $objectids): void {
        global $DB;
        $context = $courseid !== null && $DB->record_exists('course', ['id' => $courseid])
            ? \context_course::instance($courseid) : \context_system::instance();
        $other = ['tool' => $tool, 'clientid' => \core_text::substr($clientid, 0, 255), 'outcome' => $outcome];
        if ($objectids) {
            $other['objectids'] = array_values(array_map('intval', $objectids));
        }
        tool_called::create(['context' => $context, 'other' => $other])->trigger();
    }
}
