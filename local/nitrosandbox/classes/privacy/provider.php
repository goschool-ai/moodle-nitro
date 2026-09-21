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

namespace local_nitrosandbox\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider: the plugin stores one user preference (the demo course ID).
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    #[\Override]
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            \local_nitrosandbox\provisioner::PREFERENCE,
            'privacy:metadata:preference:courseid'
        );
        return $collection;
    }

    #[\Override]
    public static function export_user_preferences(int $userid) {
        $courseid = get_user_preferences(\local_nitrosandbox\provisioner::PREFERENCE, null, $userid);
        if ($courseid !== null) {
            \core_privacy\local\request\writer::export_user_preference(
                'local_nitrosandbox',
                \local_nitrosandbox\provisioner::PREFERENCE,
                $courseid,
                get_string('privacy:metadata:preference:courseid', 'local_nitrosandbox')
            );
        }
    }
}
