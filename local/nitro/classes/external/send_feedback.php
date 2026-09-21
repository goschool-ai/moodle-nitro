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

namespace local_nitro\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_nitro\local\confirmation;
use local_nitro\local\mailer;
use local_nitro\local\plugin;
use local_nitro\task\send_feedback_mail;
use local_nitro\oauth\authorization;

/**
 * Tool send_feedback: what a teacher found missing or broken in nitro, mailed to the nitro team.
 *
 * Carries the teacher's text and the site and version information, never course or student data, and
 * only after the teacher approved the exact message.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_feedback extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'message' => new external_value(PARAM_RAW, 'What is missing or broken, in the teacher\'s words or yours. '
                . 'Describe what the teacher wanted to do and what nitro could not do. Write it so it is useful '
                . 'without the course: no student names, no course content. If the teacher wants an answer, ask them '
                . 'whether to include their email address in the text.'),
            'tool' => new external_value(PARAM_ALPHANUMEXT, 'The tool this is about, if any, for example '
                . 'list_submissions', VALUE_DEFAULT, ''),
            'confirmation_token' => confirmation::param(),
        ]);
    }

    /**
     * Previews or sends the feedback.
     *
     * @param string $message
     * @param string $tool
     * @param string $confirmationtoken
     * @return array
     */
    public static function execute(string $message, string $tool = '', string $confirmationtoken = ''): array {
        global $CFG, $USER, $SITE;
        $params = self::validate_parameters(self::execute_parameters(), [
            'message' => $message, 'tool' => $tool, 'confirmation_token' => $confirmationtoken,
        ]);
        self::validate_context(\context_system::instance());
        if (!plugin::active() || !get_config('local_nitro', 'feedbackenabled')) {
            throw new \moodle_exception('feedbackoff', 'local_nitro');
        }
        if (!authorization::user_may_connect($USER->id)) {
            throw new \required_capability_exception(\context_system::instance(), 'local/nitro:use', 'nopermissions', '');
        }
        $text = trim($params['message']);
        if ($text === '' || \core_text::strlen($text) > 5000) {
            throw new \invalid_parameter_exception('message must be 1 to 5000 characters.');
        }

        $release = \core_plugin_manager::instance()->get_plugin_info('local_nitro')->release;
        $body = $text . "\n\n--\n"
            . "site: " . format_string($SITE->fullname) . ' (' . $CFG->wwwroot . ")\n"
            . "nitro: {$release}\n"
            . 'moodle: ' . $CFG->release . "\n"
            . ($params['tool'] !== '' ? "tool: {$params['tool']}\n" : '');
        $to = trim((string) get_config('local_nitro', 'feedbackemail'));
        if ($to === '' || !validate_email($to)) {
            throw new \moodle_exception('feedbacknoaddress', 'local_nitro');
        }
        $subject = 'nitro feedback' . ($params['tool'] !== '' ? ": {$params['tool']}" : '');

        $base = ['recipient' => $to, 'message' => $body, 'subject' => $subject];
        if ($params['confirmation_token'] === '') {
            return $base + confirmation::result_fields(confirmation::issue('send_feedback', $params))
                + ['sent' => false, 'queued' => false, 'note' => ''];
        }

        confirmation::redeem('send_feedback', $params);
        $sent = mailer::send($to, $subject, $body);
        if (!$sent) {
            // The mail server was not there; Moodle retries the queued task, so the report is not lost.
            send_feedback_mail::queue($to, $subject, $body);
        }
        return $base + confirmation::result_fields(null) + [
            'sent' => $sent,
            'queued' => !$sent,
            'note' => $sent ? '' : get_string('feedbackqueued', 'local_nitro'),
        ];
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(confirmation::result_returns() + [
            'recipient' => new external_value(PARAM_RAW, 'Where it goes'),
            'subject' => new external_value(PARAM_TEXT, 'Subject of the message'),
            'message' => new external_value(PARAM_RAW, 'The exact message; show it to the teacher before confirming'),
            'sent' => new external_value(PARAM_BOOL, 'True once the mail server has taken the message'),
            'queued' => new external_value(PARAM_BOOL, 'True when the mail server could not be reached and '
                . 'the message is waiting to be sent again; it is not lost'),
            'note' => new external_value(PARAM_TEXT, 'What to tell the teacher when the message did not go '
                . 'out immediately; empty otherwise'),
        ]);
    }
}
