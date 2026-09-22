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

namespace local_nitrosandbox;

/**
 * Starts provisioning for new sandbox users.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * A user logged in: provision if they are confirmed and have no demo course yet.
     *
     * @param \core\event\user_loggedin $event
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        self::maybe_queue((int) $event->objectid);
    }

    /**
     * A user was created: provision if the account is already confirmed.
     *
     * @param \core\event\user_created $event
     */
    public static function user_created(\core\event\user_created $event): void {
        self::maybe_queue((int) $event->objectid);
    }

    /**
     * Queues the provisioning task once per user.
     *
     * @param int $userid
     */
    private static function maybe_queue(int $userid): void {
        global $DB;
        if (!get_config('local_nitrosandbox', 'enabled')) {
            return;
        }
        $user = $DB->get_record(
            'user',
            ['id' => $userid, 'deleted' => 0],
            'id, confirmed, auth, suspended, timecreated'
        );
        if (
            !$user || !$user->confirmed || $user->suspended || $user->auth === 'nologin'
                || isguestuser($user) || is_siteadmin($user)
        ) {
            return;
        }
        // Only accounts created after onboarding was switched on: the site's existing users are left alone.
        if ($user->timecreated < (int) get_config('local_nitrosandbox', 'onboardfrom')) {
            return;
        }
        if (get_user_preferences(provisioner::PREFERENCE, null, $user->id) !== null) {
            return;
        }
        // Mark first, so a second login before cron does not queue another course.
        set_user_preference(provisioner::PREFERENCE, 0, $user->id);
        $task = new task\provision_course();
        $task->set_custom_data(['userid' => $user->id]);
        $task->set_userid(get_admin()->id);
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
