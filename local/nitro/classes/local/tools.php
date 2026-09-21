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

namespace local_nitro\local;

/**
 * The tools nitro can expose, and which of them the site allows.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tools {
    /** @var string[] Every tool name, in the order they are listed to clients. */
    public const ALL = [
        'list_courses',
        'course_overview',
        'list_participants',
        'list_submissions',
        'list_forum_posts',
        'read_activity',
        'save_page',
        'save_assignment',
        'import_questions',
        'create_quiz',
        'add_questions_to_quiz',
        'message_students',
        'post_announcement',
        'grade_submission',
        'send_feedback',
    ];

    /** @var string[] Tools that only read. */
    public const READ = ['list_courses', 'course_overview', 'list_participants', 'list_submissions',
        'list_forum_posts', 'read_activity'];

    /** @var string[] Tools that reach students and therefore need a confirmed preview. */
    public const CONFIRM = ['message_students', 'post_announcement', 'grade_submission'];

    /** @var string[] Tools that reach outside Moodle and are previewed, but do not touch the course. */
    public const CONFIRM_ONLY = ['send_feedback'];

    /** @var array<string, string> Human-readable tool titles (Cowork shows them on confirmation prompts). */
    public const TITLES = [
        'list_courses' => 'List my courses',
        'course_overview' => 'Show course contents',
        'list_participants' => 'List participants',
        'list_submissions' => 'List assignment submissions',
        'list_forum_posts' => 'Read forum discussions',
        'read_activity' => 'Read an activity',
        'save_page' => 'Create or update a page',
        'save_assignment' => 'Create or update an assignment',
        'import_questions' => 'Import questions',
        'create_quiz' => 'Create a quiz',
        'add_questions_to_quiz' => 'Add questions to a quiz',
        'message_students' => 'Message students',
        'post_announcement' => 'Post an announcement',
        'grade_submission' => 'Grade submissions',
        'send_feedback' => 'Send feedback about nitro',
    ];

    /**
     * External function behind a tool.
     *
     * @param string $tool
     * @return string
     */
    public static function function_name(string $tool): string {
        return 'local_nitro_' . $tool;
    }

    /**
     * Tool annotations (MCP 2025-06-18 and later) describing what a tool does.
     *
     * @param string $tool
     * @return array
     */
    public static function annotations(string $tool): array {
        $read = in_array($tool, self::READ, true);
        return [
            'title' => self::TITLES[$tool] ?? $tool,
            'readOnlyHint' => $read,
            'destructiveHint' => in_array($tool, array_merge(self::CONFIRM, self::CONFIRM_ONLY), true),
            'idempotentHint' => $read || in_array($tool, ['save_page', 'save_assignment'], true),
            'openWorldHint' => false,
        ];
    }

    /**
     * Tool names on the site's allowlist.
     *
     * @return string[]
     */
    public static function allowed(): array {
        $setting = get_config('local_nitro', 'tools');
        if ($setting === false) {
            $setting = implode(',', self::ALL);
        }
        $chosen = array_filter(explode(',', $setting));
        $allowed = array_values(array_intersect(self::ALL, $chosen));
        if (!get_config('local_nitro', 'feedbackenabled')) {
            $allowed = array_values(array_diff($allowed, ['send_feedback']));
        }
        return $allowed;
    }

    /**
     * Choices for the allowlist setting: every tool, keyed and labelled by its name.
     *
     * @return array<string, string>
     */
    public static function choices(): array {
        return array_combine(self::ALL, self::ALL);
    }
}
