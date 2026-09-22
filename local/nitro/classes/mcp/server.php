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

namespace local_nitro\mcp;

use local_nitro\oauth\metadata;
use local_nitro\oauth\tokens;

/**
 * The MCP endpoint: Streamable HTTP transport, one JSON response per request, tools only.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class server {
    /** @var string[] Supported protocol versions, newest first. */
    public const PROTOCOL_VERSIONS = ['2025-11-25', '2025-06-18', '2025-03-26'];

    /** @var string Guidance for the AI, sent as the server instructions. */
    public const INSTRUCTIONS = <<<'TEXT'
        nitro lets you work in the teacher's Moodle as the teacher, with exactly their Moodle permissions.

        Start with list_courses to find the course ID; the teacher usually names a course by its short or full name.
        Use course_overview to see sections and activities with their keys and due dates before changing anything.

        Reading (list_courses, course_overview, list_participants, list_submissions, list_forum_posts) changes nothing.
        list_forum_posts reads announcements and forum discussions with their replies, which is where a course's
        news and student questions live; course_overview only tells you that a forum exists.
        read_activity returns the text of one activity: read the course's own material with it before writing
        questions, a summary or an announcement from it, instead of asking the teacher to paste the text.

        Writing content (save_page, save_assignment, import_questions, create_quiz, add_questions_to_quiz) changes
        the course. When unsure, call it with dry_run: true first and show the teacher what would change.
        Pages and assignments are identified by a stable key: calling save_page or save_assignment again with the same
        key updates the same activity instead of creating a new one.

        For a quiz: import the questions first (import_questions creates the category), then create_quiz (hidden),
        then add_questions_to_quiz, with make_visible when the teacher wants it open straight away.

        Anything that reaches students (message_students, post_announcement, grade_submission) never happens on the
        first call. The first call returns a preview and a confirmation_token. Show the preview to the teacher, word
        for word, and ask for explicit approval. Only after the teacher approves, call the tool again with exactly the
        same arguments plus confirmation_token. If the teacher asks for any change, make a new preview.

        If the teacher wants something nitro has no tool for, or a tool behaves wrongly, offer to report it and use
        send_feedback. It mails the nitro team the text you write, with the site name and the version numbers, and
        nothing else: never course content or student data. Like the tools above, it only sends after the teacher has
        read the exact message and approved it.

        Dates are ISO 8601. Always tell the teacher the weekday and date you are about to use.
        When a tool returns an error, read the message: it says what to change.
        TEXT;

    /**
     * Handles one HTTP request to the endpoint.
     *
     * @param string $httpmethod
     * @param string $pathinfo path after mcp.php (for the appended discovery form)
     * @param string $body raw request body
     * @param array $headers request headers, names lower-cased
     * @param string|null $token bearer token
     * @return array{status: int, headers: array<string, string>, body: ?string}
     */
    public static function handle(string $httpmethod, string $pathinfo, string $body, array $headers, ?string $token): array {
        global $CFG;
        if (!\local_nitro\local\plugin::active() || !empty($CFG->maintenance_enabled)) {
            return self::http(503, null, ['Retry-After' => '3600']);
        }
        // Some clients look for discovery documents appended to the resource URL.
        if ($httpmethod === 'GET' && str_contains($pathinfo, '/.well-known/')) {
            return self::http(200, metadata::for_path($pathinfo));
        }
        if ($httpmethod !== 'POST') {
            return self::http(405, null, ['Allow' => 'POST']);
        }

        $grant = $token === null ? null : tokens::validate_access($token);
        if ($grant === null) {
            $challenge = 'Bearer resource_metadata="' . metadata::resource_metadata_url() . '", scope="'
                . implode(' ', metadata::SCOPES) . '"';
            if ($token !== null) {
                $challenge .= ', error="invalid_token"';
            }
            return self::http(401, ['error' => 'invalid_token'], ['WWW-Authenticate' => $challenge]);
        }

        $version = $headers['mcp-protocol-version'] ?? null;
        if ($version !== null && !in_array($version, self::PROTOCOL_VERSIONS, true)) {
            return self::http(400, self::error(
                null,
                -32600,
                'Unsupported MCP-Protocol-Version.',
                ['supported' => self::PROTOCOL_VERSIONS]
            ));
        }

        $message = json_decode($body, true);
        if (!is_array($message)) {
            return self::http(400, self::error(null, -32700, 'Parse error: the body must be a JSON-RPC message.'));
        }
        if (array_is_list($message)) {
            return self::http(400, self::error(null, -32600, 'Batches are not supported.'));
        }
        if (($message['jsonrpc'] ?? null) !== '2.0' || !is_string($message['method'] ?? null)) {
            return self::http(400, self::error($message['id'] ?? null, -32600, 'Invalid JSON-RPC request.'));
        }
        if (!array_key_exists('id', $message)) {
            // A notification: acknowledged, never answered.
            return self::http(202, null);
        }

        \core\session\manager::set_user(\core_user::get_user($grant->userid, '*', MUST_EXIST));
        $id = $message['id'];
        $params = is_array($message['params'] ?? null) ? $message['params'] : [];
        $version ??= '2025-03-26';

        return match ($message['method']) {
            'initialize' => self::http(200, self::result($id, self::initialize($params))),
            'ping' => self::http(200, self::result($id, new \stdClass())),
            'tools/list' => self::http(200, self::result($id, ['tools' => registry::list($version)])),
            'tools/call' => self::http(200, self::call_tool($id, $params, $grant->clientid, $version)),
            default => self::http(200, self::error($id, -32601, "Method not found: {$message['method']}")),
        };
    }

    /**
     * The initialize result.
     *
     * @param array $params
     * @return array
     */
    private static function initialize(array $params): array {
        $requested = $params['protocolVersion'] ?? null;
        $version = in_array($requested, self::PROTOCOL_VERSIONS, true) ? $requested : self::PROTOCOL_VERSIONS[0];
        return [
            'protocolVersion' => $version,
            'capabilities' => ['tools' => ['listChanged' => false]],
            'serverInfo' => [
                'name' => 'nitro',
                'title' => 'nitro for Moodle',
                'version' => (string) \core_plugin_manager::instance()->get_plugin_info('local_nitro')->release,
            ],
            'instructions' => self::INSTRUCTIONS,
        ];
    }

    /**
     * A tools/call response.
     *
     * @param mixed $id
     * @param array $params
     * @param string $clientid
     * @param string $version
     * @return array
     */
    private static function call_tool(mixed $id, array $params, string $clientid, string $version): array {
        $tool = $params['name'] ?? null;
        $info = is_string($tool) ? registry::function_info($tool) : null;
        if ($info === null) {
            return self::error($id, -32602, 'Unknown tool: ' . (is_string($tool) ? $tool : '(none)') .
                '. Call tools/list to see the available tools.');
        }
        $args = $params['arguments'] ?? [];
        if (!is_array($args) || ($args && array_is_list($args))) {
            return self::error($id, -32602, 'arguments must be an object.');
        }
        $outcome = dispatcher::call($tool, $info, $args, $clientid);
        if ($outcome['isError']) {
            return self::result($id, ['content' => [['type' => 'text', 'text' => $outcome['message']]], 'isError' => true]);
        }
        $result = [
            'content' => [['type' => 'text', 'text' => json_encode(
                $outcome['data'],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )]],
            'isError' => false,
        ];
        if ($version !== '2025-03-26') {
            $result['structuredContent'] = $outcome['data'] ?: new \stdClass();
        }
        return self::result($id, $result);
    }

    /**
     * A JSON-RPC result.
     *
     * @param mixed $id
     * @param mixed $result
     * @return array
     */
    private static function result(mixed $id, mixed $result): array {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    /**
     * A JSON-RPC error.
     *
     * @param mixed $id
     * @param int $code
     * @param string $message
     * @param array|null $data
     * @return array
     */
    private static function error(mixed $id, int $code, string $message, ?array $data = null): array {
        $error = ['code' => $code, 'message' => $message];
        if ($data !== null) {
            $error['data'] = $data;
        }
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => $error];
    }

    /**
     * An HTTP response.
     *
     * @param int $status
     * @param array|null $document JSON body, or null for an empty body
     * @param array $headers
     * @return array
     */
    private static function http(int $status, ?array $document, array $headers = []): array {
        $body = null;
        if ($document !== null) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
            $body = json_encode($document, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return ['status' => $status, 'headers' => $headers, 'body' => $body];
    }
}
