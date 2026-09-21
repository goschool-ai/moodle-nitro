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

namespace local_nitro;

/**
 * Ends a user's AI connections when their credentials change, as a password change ends their sessions.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Revokes every grant of the event's user.
     *
     * @param \core\event\base $event user_password_updated or user_deleted
     */
    public static function revoke_user_grants(\core\event\base $event): void {
        global $DB;
        $userid = (int) ($event->relateduserid ?: $event->objectid);
        foreach ($DB->get_fieldset('local_nitro_grant', 'id', ['userid' => $userid]) as $grantid) {
            oauth\tokens::revoke_grant((int) $grantid);
        }
    }
}
