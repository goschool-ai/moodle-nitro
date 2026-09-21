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
 * Dynamic client registration endpoint (RFC 7591).
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- Open registration per RFC 7591; the security boundary is the consent screen.
require(__DIR__ . '/../../../config.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Cache-Control: no-store');
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(204);
    die;
}
if (!\local_nitro\local\plugin::active()) {
    http_response_code(503);
    echo json_encode(['error' => 'temporarily_unavailable']);
    die;
}
if ($method !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'invalid_request', 'error_description' => 'Use POST with a JSON body.']);
    die;
}

$body = (string) file_get_contents('php://input', false, null, 0, \local_nitro\oauth\registration::MAX_BODY + 1);
if (strlen($body) > \local_nitro\oauth\registration::MAX_BODY) {
    http_response_code(413);
    echo json_encode(['error' => 'invalid_client_metadata', 'error_description' => 'The request body is too large.']);
    die;
}
[$status, $response] = \local_nitro\oauth\registration::handle(json_decode($body, true));
http_response_code($status);
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
