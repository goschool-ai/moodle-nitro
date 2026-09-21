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
 * Sends plain mail to an address that is not a Moodle user, and says whether it went out.
 *
 * Moodle's own mail is only ever addressed to users, so nitro builds a throwaway recipient from the
 * support user. The mail server is not always there: a name resolution or SMTP failure returns false
 * here, and the caller decides whether to queue a retry.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mailer {
    /** @var bool Set by tests to make the next send fail the way an unreachable mail server does. */
    private static bool $failnext = false;

    /**
     * Makes the next send fail, so tests can cover what happens when the mail server is not there.
     */
    public static function fail_next_send_for_testing(): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('fail_next_send_for_testing() is for tests only.');
        }
        self::$failnext = true;
    }

    /**
     * Mails one address.
     *
     * @param string $to Recipient address
     * @param string $subject
     * @param string $body Plain text
     * @return bool True once the mail server has taken it
     */
    public static function send(string $to, string $subject, string $body): bool {
        if (self::$failnext) {
            self::$failnext = false;
            return false;
        }
        $recipient = \core_user::get_support_user();
        $recipient->email = $to;
        $recipient->id = -99;
        return (bool) email_to_user($recipient, \core_user::get_noreply_user(), $subject, $body);
    }
}
