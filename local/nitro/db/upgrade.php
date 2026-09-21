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

/**
 * Upgrade steps for local_nitro.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Runs the upgrade.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool
 */
function xmldb_local_nitro_upgrade($oldversion) {
    if ($oldversion < 2026092002) {
        // The allowlist is stored at install time, so tools added later would stay invisible.
        local_nitro_allow_new_tools();
        upgrade_plugin_savepoint(true, 2026092002, 'local', 'nitro');
    }

    if ($oldversion < 2026092003) {
        local_nitro_allow_new_tools();
        upgrade_plugin_savepoint(true, 2026092003, 'local', 'nitro');
    }
    return true;
}

/**
 * Adds tools that the stored allowlist does not know yet, keeping the admin's own choices.
 */
function local_nitro_allow_new_tools() {
    $setting = get_config('local_nitro', 'tools');
    if ($setting === false) {
        return;
    }
    $known = array_filter(explode(',', $setting));
    $new = array_diff(\local_nitro\local\tools::ALL, $known);
    if ($new) {
        set_config('tools', implode(',', array_merge($known, $new)), 'local_nitro');
    }
}
