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
 * The MCP endpoint (Streamable HTTP, JSON responses, tools only).
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// WS_SERVER also makes setup.php define NO_MOODLE_COOKIES: no session, no cookies.
define('NO_DEBUG_DISPLAY', true);
define('WS_SERVER', true);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- Authenticated by an OAuth bearer token; see server::handle().
require(__DIR__ . '/../../config.php');

raise_memory_limit(MEMORY_EXTRA);
core_php_time_limit::raise(120);

$headers = [];
if (function_exists('getallheaders')) {
    foreach (getallheaders() as $name => $value) {
        $headers[strtolower($name)] = $value;
    }
}

$response = \local_nitro\mcp\server::handle(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    (string) ($_SERVER['PATH_INFO'] ?? ''),
    (string) file_get_contents('php://input'),
    $headers,
    \local_nitro\local\http::bearer_token()
);

http_response_code($response['status']);
header('Cache-Control: no-store');
foreach ($response['headers'] as $name => $value) {
    header($name . ': ' . $value);
}
if ($response['body'] !== null) {
    echo $response['body'];
}
