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

use core_external\external_api;
use local_nitro\local\tools;

/**
 * The tools a client can see and call: the allowlist, limited to implemented functions.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registry {
    /**
     * External function info for an allowlisted tool, or null if the tool cannot be called.
     *
     * @param string $tool
     * @return \stdClass|null
     */
    public static function function_info(string $tool): ?\stdClass {
        if (!in_array($tool, tools::allowed(), true)) {
            return null;
        }
        try {
            return external_api::external_function_info(tools::function_name($tool));
        } catch (\Throwable $e) {
            // Not implemented (yet): not listed and not callable.
            return null;
        }
    }

    /**
     * The tools/list result.
     *
     * @param string $protocolversion negotiated MCP protocol version
     * @return array[]
     */
    public static function list(string $protocolversion): array {
        $list = [];
        foreach (tools::allowed() as $tool) {
            $info = self::function_info($tool);
            if ($info === null) {
                continue;
            }
            $entry = [
                'name' => $tool,
                'description' => (string) $info->description,
                'inputSchema' => schema::from_description($info->parameters_desc),
            ];
            if ($protocolversion !== '2025-03-26') {
                $entry['annotations'] = tools::annotations($tool);
            }
            $list[] = $entry;
        }
        return $list;
    }
}
