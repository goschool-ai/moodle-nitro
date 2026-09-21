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
 * OAuth discovery documents: authorization server metadata and protected resource metadata.
 *
 * Reachable as the issuer itself, as `<issuer>/.well-known/openid-configuration` (slash
 * arguments, no web server change), as `<issuer>/.well-known/oauth-protected-resource`
 * (the 401 pointer), and through the optional root `/.well-known/` rewrite.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- Public discovery document, no session involved.
require(__DIR__ . '/../../../config.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if (!\local_nitro\local\plugin::active()) {
    http_response_code(503);
    header('Cache-Control: no-store');
    echo json_encode(['error' => 'temporarily_unavailable']);
    die;
}

header('Cache-Control: public, max-age=300');
echo json_encode(
    \local_nitro\oauth\metadata::for_path((string) get_file_argument()),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
