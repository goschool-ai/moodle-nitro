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
 * Admin page: register, list and delete OAuth clients by hand.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_nitro\form\client_form;
use local_nitro\oauth\clients;

admin_externalpage_setup('local_nitro_clients');

$pageurl = new moodle_url('/local/nitro/clients.php');
$delete = optional_param('delete', '', PARAM_ALPHANUM);

if ($delete !== '') {
    $record = $DB->get_record('local_nitro_client', ['clientid' => $delete], '*', MUST_EXIST);
    if (optional_param('confirm', 0, PARAM_BOOL) && confirm_sesskey()) {
        clients::delete($delete);
        redirect(
            $pageurl,
            get_string('clientdeleted', 'local_nitro', format_string($record->name)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(
        get_string('clientdeleteconfirm', 'local_nitro', format_string($record->name)),
        new moodle_url($pageurl, ['delete' => $delete, 'confirm' => 1, 'sesskey' => sesskey()]),
        $pageurl
    );
    echo $OUTPUT->footer();
    die;
}

$form = new client_form($pageurl);
if ($form->is_cancelled()) {
    redirect($pageurl);
}

if ($data = $form->get_data()) {
    [$client, $secret] = clients::register(
        'admin',
        $data->name,
        client_form::split_uris($data->redirecturis),
        $data->confidential ? 'client_secret_post' : 'none',
        $USER->id
    );
    // Post/redirect/get: the secret is shown once, and reloading the page does not register again.
    $SESSION->local_nitro_newclient = ['name' => $client->name, 'clientid' => $client->clientid, 'secret' => $secret];
    redirect($pageurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('clients', 'local_nitro'));

if (!empty($SESSION->local_nitro_newclient)) {
    $new = $SESSION->local_nitro_newclient;
    unset($SESSION->local_nitro_newclient);
    $details = html_writer::tag('p', get_string('clientregistered', 'local_nitro', format_string($new['name'])));
    $details .= html_writer::tag(
        'dl',
        html_writer::tag('dt', get_string('clientid', 'local_nitro')) .
        html_writer::tag('dd', html_writer::tag('code', s($new['clientid']))) .
        ($new['secret'] === null ? '' :
            html_writer::tag('dt', get_string('clientsecret', 'local_nitro')) .
        html_writer::tag('dd', html_writer::tag('code', s($new['secret']))))
    );
    if ($new['secret'] !== null) {
        $details .= html_writer::tag('p', get_string('clientsecretonce', 'local_nitro'));
    }
    echo $OUTPUT->notification($details, \core\output\notification::NOTIFY_SUCCESS, false);
}

$records = $DB->get_records('local_nitro_client', null, 'timecreated DESC');
if ($records) {
    $table = new html_table();
    $table->id = 'local_nitro_clients';
    $table->head = [
        get_string('clientname', 'local_nitro'),
        get_string('clientid', 'local_nitro'),
        get_string('clientorigin', 'local_nitro'),
        get_string('clientredirecturis', 'local_nitro'),
        get_string('clientfirstused', 'local_nitro'),
        get_string('actions'),
    ];
    foreach ($records as $record) {
        $uris = array_map('s', json_decode($record->redirecturis, true) ?: []);
        $table->data[] = [
            format_string($record->name),
            html_writer::tag('code', $record->clientid),
            get_string('clientorigin_' . $record->origin, 'local_nitro') .
                ($record->authmethod === 'none' ? '' : ' · ' . get_string('clientconfidential', 'local_nitro')),
            implode('<br>', $uris),
            $record->timefirstused ? userdate($record->timefirstused) : get_string('never'),
            html_writer::link(
                new moodle_url($pageurl, ['delete' => $record->clientid]),
                get_string('delete'),
                ['aria-label' => get_string('clientdelete', 'local_nitro', format_string($record->name))]
            ),
        ];
    }
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('clientsnone', 'local_nitro'), \core\output\notification::NOTIFY_INFO);
}

echo $OUTPUT->heading(get_string('clientadd', 'local_nitro'), 3);
$form->display();
echo $OUTPUT->footer();
