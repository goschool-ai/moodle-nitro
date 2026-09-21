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
 * OAuth token endpoint: authorization code exchange and refresh token rotation.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- Server-to-server token endpoint; clients authenticate per RFC 6749.
require(__DIR__ . '/../../../config.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Cache-Control: no-store');
header('Pragma: no-cache');
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
    echo json_encode(['error' => 'invalid_request', 'error_description' => 'Use POST.']);
    die;
}

// Parameters come from the form-encoded body only, never from the query string.
$params = [];
foreach ($_POST as $name => $value) {
    if (is_string($name) && is_string($value)) {
        $params[$name] = trim($value);
    }
}
$basic = \local_nitro\local\http::basic_credentials();

[$status, $response] = \local_nitro\oauth\tokens::handle($params, $basic);
if ($status === 401 && $basic !== null) {
    header('WWW-Authenticate: Basic realm="nitro"');
}
http_response_code($status);
echo json_encode($response, JSON_UNESCAPED_SLASHES);
