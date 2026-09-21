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
 * OAuth authorization endpoint: Moodle login, then the consent screen.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

use local_nitro\oauth\authorization;
use local_nitro\oauth\redirect_uris;

$params = [];
foreach (authorization::PARAMS as $name) {
    $value = optional_param($name, null, PARAM_RAW_TRIMMED);
    if ($value !== null) {
        $params[$name] = $value;
    }
}

$PAGE->set_url(new moodle_url('/local/nitro/oauth/authorize.php', $params));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('consenttitle', 'local_nitro'));
// The consent screen must never be framed, whatever the site's frame embedding setting.
header("Content-Security-Policy: frame-ancestors 'none'");
header('X-Frame-Options: DENY');

/**
 * Shows an error on the Moodle page and stops.
 *
 * @param string $message
 */
function local_nitro_authorize_fail(string $message): void {
    global $OUTPUT;
    echo $OUTPUT->header();
    echo $OUTPUT->notification(s($message), \core\output\notification::NOTIFY_ERROR, false);
    echo $OUTPUT->footer();
    die;
}

/**
 * Sends the user back to the client. Only called with an allowlisted redirect URI, and not through
 * redirect(), which runs the URL through the HTML cleaner and may show an intermediate page.
 *
 * @param string $url
 */
function local_nitro_authorize_redirect(string $url): void {
    \core\session\manager::write_close();
    header('Location: ' . $url, true, 302);
    header('Cache-Control: no-store');
    die;
}

if (!\local_nitro\local\plugin::active()) {
    local_nitro_authorize_fail(get_string('autherror_disabled', 'local_nitro'));
}

// Every configured auth method and SSO works through the normal login; guests cannot connect.
require_login(null, false);
if (isguestuser()) {
    require_logout();
    redirect(get_login_url());
}

$request = authorization::validate($params);
if (isset($request['error'])) {
    if ($request['redirect']) {
        local_nitro_authorize_redirect(authorization::error_url($params, $request));
    }
    local_nitro_authorize_fail($request['description']);
}

if (!authorization::user_may_connect($USER->id)) {
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_nitro/notpermitted', []);
    echo $OUTPUT->footer();
    die;
}

$decision = optional_param('decision', '', PARAM_ALPHA);
if ($decision !== '' && confirm_sesskey()) {
    if ($decision === 'approve') {
        local_nitro_authorize_redirect(authorization::approve($request, $USER->id));
    }
    local_nitro_authorize_redirect(authorization::deny($request));
}

$client = $request['client'];
$notice = trim((string) get_config('local_nitro', 'consentnotice'));
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_nitro/consent', [
    'clientname' => $client->name,
    'redirecthost' => redirect_uris::host($request['redirecturi']),
    'loopback' => redirect_uris::is_loopback($request['redirecturi']),
    'selfregistered' => $client->origin === 'dcr',
    'offline' => in_array('offline_access', explode(' ', $request['scope']), true),
    'username' => fullname($USER),
    'notice' => $notice === '' ? null : format_text($notice, FORMAT_PLAIN),
    'action' => (new moodle_url('/local/nitro/oauth/authorize.php'))->out(false),
    'sesskey' => sesskey(),
    'params' => array_map(fn($k, $v) => ['name' => $k, 'value' => $v], array_keys($params), $params),
]);
echo $OUTPUT->footer();
