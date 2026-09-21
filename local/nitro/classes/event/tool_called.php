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

namespace local_nitro\event;

/**
 * An AI client called a nitro tool.
 *
 * Only identifiers are stored, never message bodies, page content or question text.
 *
 * @property-read array $other {
 *      - string tool: tool name
 *      - string clientid: OAuth client ID (or CIMD URL)
 *      - string outcome: success, error or preview
 *      - int[] objectids: optional, IDs of the affected objects (for example recipient user IDs)
 * }
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_called extends \core\event\base {
    /** @var string[] Outcomes a tool call can have. */
    public const OUTCOMES = ['success', 'error', 'preview'];

    #[\Override]
    protected function init() {
        // Same as core\event\webservice_function_called: a call may read or write.
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    #[\Override]
    public static function get_name() {
        return get_string('eventtoolcalled', 'local_nitro');
    }

    #[\Override]
    public function get_description() {
        return "The user with id '{$this->userid}' called the nitro tool '{$this->other['tool']}' " .
            "through the client '{$this->other['clientid']}' with outcome '{$this->other['outcome']}'.";
    }

    #[\Override]
    protected function validate_data() {
        parent::validate_data();
        foreach (['tool', 'clientid', 'outcome'] as $key) {
            if (!isset($this->other[$key])) {
                throw new \coding_exception("The '{$key}' value must be set in other.");
            }
        }
        if (!in_array($this->other['outcome'], self::OUTCOMES, true)) {
            throw new \coding_exception("Unknown outcome '{$this->other['outcome']}'.");
        }
        $allowed = ['tool', 'clientid', 'outcome', 'objectids'];
        if ($extra = array_diff(array_keys($this->other), $allowed)) {
            throw new \coding_exception('Only identifiers may be logged; unexpected keys: ' . implode(', ', $extra));
        }
    }

    #[\Override]
    public static function get_other_mapping() {
        return false;
    }
}
