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
 * The user's connected AI tools: see and revoke approved clients.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests cannot connect AI tools.');
}

$pageurl = new moodle_url('/local/nitro/connections.php');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('connections', 'local_nitro'));
$PAGE->set_heading(fullname($USER));
$PAGE->navbar->add(get_string('profile'), new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('connections', 'local_nitro'));

$revoke = optional_param('revoke', 0, PARAM_INT);
if ($revoke) {
    $grant = $DB->get_record('local_nitro_grant', ['id' => $revoke, 'userid' => $USER->id], '*', MUST_EXIST);
    if (optional_param('confirm', 0, PARAM_BOOL) && confirm_sesskey()) {
        \local_nitro\oauth\tokens::revoke_grant($grant->id);
        redirect(
            $pageurl,
            get_string('connectionrevoked', 'local_nitro', format_string($grant->clientname)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(
        get_string('connectionrevokeconfirm', 'local_nitro', format_string($grant->clientname)),
        new single_button(
            new moodle_url($pageurl, ['revoke' => $grant->id, 'confirm' => 1, 'sesskey' => sesskey()]),
            get_string('connectionrevoke', 'local_nitro'),
            'post',
            single_button::BUTTON_DANGER
        ),
        $pageurl
    );
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('connections', 'local_nitro'));
echo html_writer::tag('p', get_string('connectionsintro', 'local_nitro'));

$grants = $DB->get_records('local_nitro_grant', ['userid' => $USER->id], 'timecreated DESC');
if (!$grants) {
    echo $OUTPUT->notification(get_string('connectionsnone', 'local_nitro'), \core\output\notification::NOTIFY_INFO);
} else {
    $table = new html_table();
    $table->id = 'local_nitro_connections';
    $table->head = [
        get_string('connectionclient', 'local_nitro'),
        get_string('connectionreturnsto', 'local_nitro'),
        get_string('connectionapproved', 'local_nitro'),
        get_string('connectionlastused', 'local_nitro'),
        '',
    ];
    foreach ($grants as $grant) {
        $table->data[] = [
            format_string($grant->clientname),
            s($grant->redirecthost),
            userdate($grant->timecreated),
            $grant->timelastused ? userdate($grant->timelastused) : get_string('never'),
            $OUTPUT->single_button(
                new moodle_url($pageurl, ['revoke' => $grant->id]),
                get_string('connectionrevoke', 'local_nitro'),
                'get'
            ),
        ];
    }
    echo html_writer::table($table);
}
echo $OUTPUT->footer();
