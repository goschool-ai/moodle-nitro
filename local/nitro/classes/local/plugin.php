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
 * Site-level state of the plugin.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin {
    /**
     * Whether the admin has switched nitro on (the site kill switch).
     *
     * The setting is called `active`, not `enabled`: core treats a plugin's `enabled` config
     * as its own plugin state and purges caches whenever it changes.
     *
     * @return bool
     */
    public static function active(): bool {
        return (bool) get_config('local_nitro', 'active');
    }
}
