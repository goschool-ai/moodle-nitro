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

namespace local_nitro\task;

use local_nitro\local\mailer;

/**
 * Mails a feedback message that could not go out when the teacher approved it.
 *
 * Failing here is what keeps the report alive: Moodle reschedules a failed ad hoc task with a growing
 * delay, so a mail server that is briefly unreachable costs time, not the teacher's report.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_feedback_mail extends \core\task\adhoc_task {
    #[\Override]
    public function get_name() {
        return get_string('tasksendfeedbackmail', 'local_nitro');
    }

    #[\Override]
    public function execute() {
        $data = $this->get_custom_data();
        if (!mailer::send($data->to, $data->subject, $data->body)) {
            throw new \moodle_exception('feedbackmailfailed', 'local_nitro', '', $data->to);
        }
        mtrace("Feedback about nitro mailed to {$data->to}.");
    }

    /**
     * Queues one message for a later attempt.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     */
    public static function queue(string $to, string $subject, string $body): void {
        $task = new self();
        $task->set_custom_data(['to' => $to, 'subject' => $subject, 'body' => $body]);
        \core\task\manager::queue_adhoc_task($task);
    }
}
