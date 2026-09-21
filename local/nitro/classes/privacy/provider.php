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

namespace local_nitro\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider: which AI clients a user approved (grants), their tokens (hashes only),
 * pending authorization codes and confirmations. All of it belongs to the user's own context.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /** @var string[] Tables with a userid column, cleared on deletion. */
    private const USER_TABLES = ['local_nitro_grant', 'local_nitro_code', 'local_nitro_confirm'];

    #[\Override]
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_nitro_grant', [
            'userid' => 'privacy:metadata:grant:userid',
            'clientid' => 'privacy:metadata:grant:clientid',
            'clientname' => 'privacy:metadata:grant:clientname',
            'redirecthost' => 'privacy:metadata:grant:redirecthost',
            'scope' => 'privacy:metadata:grant:scope',
            'timecreated' => 'privacy:metadata:grant:timecreated',
            'timelastused' => 'privacy:metadata:grant:timelastused',
        ], 'privacy:metadata:grant');
        $collection->add_database_table('local_nitro_client', [
            'createdby' => 'privacy:metadata:client:createdby',
        ], 'privacy:metadata:client');
        $collection->add_database_table('local_nitro_token', [
            'grantid' => 'privacy:metadata:token:grantid',
            'expires' => 'privacy:metadata:token:expires',
        ], 'privacy:metadata:token');
        $collection->add_database_table('local_nitro_code', [
            'userid' => 'privacy:metadata:code:userid',
            'clientid' => 'privacy:metadata:grant:clientid',
            'expires' => 'privacy:metadata:token:expires',
        ], 'privacy:metadata:code');
        $collection->add_database_table('local_nitro_confirm', [
            'userid' => 'privacy:metadata:confirm:userid',
            'tool' => 'privacy:metadata:confirm:tool',
            'timecreated' => 'privacy:metadata:grant:timecreated',
        ], 'privacy:metadata:confirm');
        return $collection;
    }

    #[\Override]
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        foreach (self::USER_TABLES as $table) {
            $contextlist->add_from_sql("SELECT ctx.id FROM {context} ctx
                  JOIN {{$table}} t ON t.userid = ctx.instanceid AND ctx.contextlevel = :level
                 WHERE t.userid = :userid", ['level' => CONTEXT_USER, 'userid' => $userid]);
        }
        return $contextlist;
    }

    #[\Override]
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ($context instanceof \context_user) {
            foreach (self::USER_TABLES as $table) {
                $userlist->add_from_sql(
                    'userid',
                    "SELECT userid FROM {{$table}} WHERE userid = :userid",
                    ['userid' => $context->instanceid]
                );
            }
        }
    }

    #[\Override]
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = (int) $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_user || (int) $context->instanceid !== $userid) {
                continue;
            }
            $grants = array_map(fn($grant) => [
                'client' => $grant->clientname,
                'clientid' => $grant->clientid,
                'returns_to' => $grant->redirecthost,
                'scope' => $grant->scope,
                'approved' => transform::datetime($grant->timecreated),
                'last_used' => $grant->timelastused ? transform::datetime($grant->timelastused) : null,
            ], $DB->get_records('local_nitro_grant', ['userid' => $userid], 'timecreated'));
            if ($grants) {
                writer::with_context($context)->export_data(
                    [get_string('connections', 'local_nitro')],
                    (object) ['connections' => array_values($grants)]
                );
            }
        }
    }

    #[\Override]
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_user) {
            self::delete_user((int) $context->instanceid);
        }
    }

    #[\Override]
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $userid = (int) $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof \context_user && (int) $context->instanceid === $userid) {
                self::delete_user($userid);
            }
        }
    }

    #[\Override]
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();
        if ($context instanceof \context_user && in_array((int) $context->instanceid, $userlist->get_userids())) {
            self::delete_user((int) $context->instanceid);
        }
    }

    /**
     * Deletes everything nitro holds about a user, which also ends all their AI connections.
     *
     * @param int $userid
     */
    private static function delete_user(int $userid): void {
        global $DB;
        foreach ($DB->get_fieldset('local_nitro_grant', 'id', ['userid' => $userid]) as $grantid) {
            \local_nitro\oauth\tokens::revoke_grant((int) $grantid);
        }
        $DB->delete_records('local_nitro_code', ['userid' => $userid]);
        $DB->delete_records('local_nitro_confirm', ['userid' => $userid]);
        // Clients an admin registered stay; only the reference to the admin goes.
        $DB->set_field('local_nitro_client', 'createdby', null, ['createdby' => $userid]);
    }
}
