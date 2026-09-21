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

namespace local_nitro\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_nitro\local\access;
use local_nitro\local\dates;

/**
 * Tool list_submissions: every student's submission and grading state for an assignment.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_submissions extends external_api {
    /** @var string[] Accepted filters. */
    public const FILTERS = ['all', 'missing', 'late', 'ungraded', 'graded'];

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID of the assignment, from course_overview'),
            'filter' => new external_value(PARAM_ALPHA, 'all; missing (nothing submitted); late (submitted after the '
                . 'due date and any extension); ungraded (submitted, not graded yet); graded', VALUE_DEFAULT, 'all'),
            'include_content' => new external_value(PARAM_BOOL, 'Also return the submitted online text, the links in it '
                . 'and the names and sizes of submitted files', VALUE_DEFAULT, false),
            'groupid' => new external_value(PARAM_INT, 'Only members of this group; 0 for everyone', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Lists submissions.
     *
     * @param int $cmid
     * @param string $filter
     * @param bool $includecontent
     * @param int $groupid
     * @return array
     */
    public static function execute(int $cmid, string $filter = 'all', bool $includecontent = false, int $groupid = 0): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $params = self::validate_parameters(
            self::execute_parameters(),
            ['cmid' => $cmid, 'filter' => $filter, 'include_content' => $includecontent, 'groupid' => $groupid]
        );
        if (!in_array($params['filter'], self::FILTERS, true)) {
            throw new \invalid_parameter_exception('filter must be one of ' . implode(', ', self::FILTERS) . '.');
        }
        $context = access::require_module($params['cmid']);
        [$course, $cm] = get_course_and_cm_from_cmid($params['cmid'], 'assign');
        require_capability('mod/assign:grade', $context);

        $assign = new \assign($context, $cm, $course);
        $instance = $assign->get_instance();
        $onlinetext = $assign->get_submission_plugin_by_type('onlinetext');
        $withtext = $onlinetext && $onlinetext->is_enabled();
        $fs = get_file_storage();

        $students = [];
        $counts = ['students' => 0, 'submitted' => 0, 'missing' => 0, 'late' => 0, 'graded' => 0];
        $visible = access::visible_users((int) $course->id, get_fast_modinfo($course)->get_cm($cm->id), $params['groupid']);
        foreach ($assign->list_participants($params['groupid'], false) as $user) {
            if ($visible !== null && !in_array((int) $user->id, $visible, true)) {
                continue;
            }
            $userinstance = $assign->get_instance($user->id);
            $submission = $instance->teamsubmission
                ? $assign->get_group_submission($user->id, 0, false)
                : $assign->get_user_submission($user->id, false);
            $flags = $assign->get_user_flags($user->id, false);
            $grade = $assign->get_user_grade($user->id, false);

            $status = $submission ? $submission->status : 'new';
            $submitted = $status === ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            $extension = $flags && $flags->extensionduedate ? (int) $flags->extensionduedate : null;
            $due = (int) $userinstance->duedate;
            $deadline = max($due, (int) $extension);
            $timesubmitted = $submitted ? (int) $submission->timemodified : null;
            $late = $submitted && $deadline && $timesubmitted > $deadline;
            $overdue = !$submitted && $deadline && $deadline < time();
            $graded = $grade && $grade->grade !== null && (float) $grade->grade >= 0;

            $counts['students']++;
            $counts['submitted'] += (int) $submitted;
            $counts['missing'] += (int) !$submitted;
            $counts['late'] += (int) $late;
            $counts['graded'] += (int) $graded;

            $keep = match ($params['filter']) {
                'missing' => !$submitted,
                'late' => $late,
                'ungraded' => $submitted && !$graded,
                'graded' => $graded,
                default => true,
            };
            if (!$keep) {
                continue;
            }

            $entry = [
                'userid' => (int) $user->id,
                'fullname' => fullname($user),
                'status' => $status === 'new' ? 'notsubmitted' : $status,
                'timesubmitted' => dates::iso($timesubmitted),
                'duedate' => dates::iso($due ?: null),
                'extension' => dates::iso($extension),
                'late' => $late,
                'overdue' => $overdue,
                'graded' => $graded,
                'grade' => $graded ? trim(html_to_text($assign->display_grade($grade->grade, false, $user->id), 0, false)) : null,
                'workflowstate' => $flags && $flags->workflowstate ? (string) $flags->workflowstate : null,
            ];
            if ($params['include_content']) {
                $entry['onlinetext'] = null;
                $entry['links'] = [];
                $entry['files'] = [];
                if ($submission && $withtext) {
                    $html = (string) $onlinetext->get_editor_text('onlinetext', $submission->id);
                    if ($html !== '') {
                        $entry['onlinetext'] = trim(html_to_text($html, 0, false));
                        $entry['links'] = self::links($html);
                    }
                }
                if ($submission) {
                    foreach (
                        $fs->get_area_files(
                            $context->id,
                            'assignsubmission_file',
                            'submission_files',
                            $submission->id,
                            'filename',
                            false
                        ) as $file
                    ) {
                        $entry['files'][] = ['name' => $file->get_filename(), 'size' => (int) $file->get_filesize()];
                    }
                }
            }
            $students[] = $entry;
        }

        return [
            'assignment' => [
                'cmid' => (int) $cm->id,
                'name' => format_string($instance->name, true, ['context' => $context]),
                'duedate' => dates::iso((int) $instance->duedate ?: null),
                'cutoffdate' => dates::iso((int) $instance->cutoffdate ?: null),
                'maxgrade' => (float) $instance->grade,
            ],
            'counts' => $counts,
            'students' => $students,
        ];
    }

    /**
     * Distinct URLs in a submission: link targets and bare URLs in the text.
     *
     * @param string $html
     * @return string[]
     */
    public static function links(string $html): array {
        preg_match_all('~href\s*=\s*["\']([^"\']+)["\']~i', $html, $hrefs);
        preg_match_all('~\bhttps?://[^\s<>"\']+~i', strip_tags(str_replace('<', ' <', $html)), $bare);
        $urls = array_map(fn($url) => rtrim(html_entity_decode($url), '.,;:)'), array_merge($hrefs[1], $bare[0]));
        return array_values(array_unique(array_filter($urls, fn($url) => preg_match('~^https?://~i', $url))));
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $optional = fn($desc) => new external_value(PARAM_RAW, $desc, VALUE_REQUIRED, null, NULL_ALLOWED);
        return new external_single_structure([
            'assignment' => new external_single_structure([
                'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'duedate' => $optional('Due date, ISO 8601'),
                'cutoffdate' => $optional('Cut-off date, ISO 8601; after it nothing can be submitted'),
                'maxgrade' => new external_value(PARAM_FLOAT, 'Maximum grade in points; negative for a scale'),
            ]),
            'counts' => new external_single_structure([
                'students' => new external_value(PARAM_INT, 'Students in the list before filtering'),
                'submitted' => new external_value(PARAM_INT, 'Submitted'),
                'missing' => new external_value(PARAM_INT, 'Not submitted'),
                'late' => new external_value(PARAM_INT, 'Submitted late'),
                'graded' => new external_value(PARAM_INT, 'Graded'),
            ]),
            'students' => new external_multiple_structure(new external_single_structure([
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'fullname' => new external_value(PARAM_TEXT, 'Full name'),
                'status' => new external_value(PARAM_ALPHA, 'notsubmitted, draft, submitted or reopened'),
                'timesubmitted' => $optional('When it was submitted, ISO 8601'),
                'duedate' => $optional('Due date for this student (with overrides), ISO 8601'),
                'extension' => $optional('Extension granted to this student, ISO 8601'),
                'overdue' => new external_value(PARAM_BOOL, 'Nothing submitted and the deadline has passed'),
                'late' => new external_value(PARAM_BOOL, 'Submitted after the due date and any extension'),
                'graded' => new external_value(PARAM_BOOL, 'Has a grade'),
                'grade' => $optional('Grade as shown in Moodle'),
                'workflowstate' => $optional('Marking workflow state, when marking workflow is on'),
                'onlinetext' => new external_value(
                    PARAM_RAW,
                    'Submitted online text as plain text',
                    VALUE_OPTIONAL,
                    null,
                    NULL_ALLOWED
                ),
                'links' => new external_multiple_structure(
                    new external_value(PARAM_RAW, 'URL'),
                    'Links in the text',
                    VALUE_OPTIONAL
                ),
                'files' => new external_multiple_structure(new external_single_structure([
                    'name' => new external_value(PARAM_FILE, 'File name'),
                    'size' => new external_value(PARAM_INT, 'Size in bytes'),
                ]), 'Submitted files', VALUE_OPTIONAL),
            ])),
        ]);
    }
}
