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
use local_nitro\local\content;
use local_nitro\local\dates;
use local_nitro\local\dry_run;
use local_nitro\local\modules;

/**
 * Tool save_assignment: create or update an Assignment by its key, without silent defaults.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_assignment extends external_api {
    /** @var string[] Submission types nitro can switch on. */
    public const SUBMISSION_TYPES = ['onlinetext', 'file'];

    /** @var string Value that clears a date. */
    private const NONE = 'none';

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        $keep = ' On update, omit to keep it.';
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID from list_courses'),
            'key' => new external_value(PARAM_RAW, 'Stable key of the assignment in this course, for example hw2. '
                . 'The same key updates the same assignment.'),
            'name' => new external_value(PARAM_TEXT, 'Name. Required when creating.' . $keep, VALUE_DEFAULT, null),
            'description' => new external_value(PARAM_RAW, 'Description in markdown.' . $keep, VALUE_DEFAULT, null),
            'section' => new external_value(
                PARAM_INT,
                'Section number. Default 0 when creating.' . $keep,
                VALUE_DEFAULT,
                null
            ),
            'visible' => new external_value(
                PARAM_BOOL,
                'Visible to students. Default true when creating.' . $keep,
                VALUE_DEFAULT,
                null
            ),
            'submission_types' => new external_multiple_structure(
                new external_value(PARAM_ALPHA, 'onlinetext or file'),
                'What students submit: onlinetext, file, or both. Required when creating.' . $keep,
                VALUE_DEFAULT,
                []
            ),
            'max_points' => new external_value(PARAM_FLOAT, 'Grade out of this many points. When creating, give '
                . 'max_points or scale.' . $keep, VALUE_DEFAULT, null),
            'scale' => new external_value(PARAM_TEXT, 'Grade with this scale instead of points, by its name as in '
                . 'Moodle.' . $keep, VALUE_DEFAULT, null),
            'due' => new external_value(PARAM_RAW, 'Due date, ISO 8601 (for example 2026-10-09T23:59, in the teacher\'s '
                . 'time zone). Submissions after it are marked late. "none" removes it.' . $keep, VALUE_DEFAULT, null),
            'cutoff' => new external_value(PARAM_RAW, 'Cut-off date, ISO 8601: after it nothing can be submitted. '
                . 'Must not be before the due date. "none" removes it.' . $keep, VALUE_DEFAULT, null),
            'dry_run' => dry_run::param(),
        ]);
    }

    /**
     * Creates or updates the assignment.
     *
     * @param int $courseid
     * @param string $key
     * @param string|null $name
     * @param string|null $description
     * @param int|null $section
     * @param bool|null $visible
     * @param array|null $submissiontypes
     * @param float|null $maxpoints
     * @param string|null $scale
     * @param string|null $due
     * @param string|null $cutoff
     * @param bool $dryrun
     * @return array
     */
    public static function execute(
        int $courseid,
        string $key,
        ?string $name = null,
        ?string $description = null,
        ?int $section = null,
        ?bool $visible = null,
        ?array $submissiontypes = null,
        ?float $maxpoints = null,
        ?string $scale = null,
        ?string $due = null,
        ?string $cutoff = null,
        bool $dryrun = false
    ): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        // Omitted (null) arguments are left out, so their defaults apply as in an MCP call.
        $params = self::validate_parameters(self::execute_parameters(), array_filter([
            'courseid' => $courseid, 'key' => $key, 'name' => $name, 'description' => $description,
            'section' => $section, 'visible' => $visible, 'submission_types' => $submissiontypes,
            'max_points' => $maxpoints, 'scale' => $scale, 'due' => $due, 'cutoff' => $cutoff, 'dry_run' => $dryrun,
        ], fn($value) => $value !== null));
        $context = access::require_course($params['courseid']);
        require_capability('moodle/course:manageactivities', $context);
        $key = modules::check_key($params['key']);
        if ($params['section'] !== null) {
            modules::check_section($params['courseid'], $params['section']);
        }
        // An empty list means "not given": an assignment without any submission type is never wanted.
        $types = $params['submission_types'] ?: null;
        if ($types !== null) {
            $types = array_values(array_unique($types));
            if (array_diff($types, self::SUBMISSION_TYPES)) {
                throw new \invalid_parameter_exception('submission_types may contain onlinetext and file.');
            }
        }
        if ($params['max_points'] !== null && $params['scale'] !== null) {
            throw new \invalid_parameter_exception('Give either max_points or scale, not both.');
        }
        if ($params['max_points'] !== null && ($params['max_points'] <= 0 || $params['max_points'] > 10000)) {
            throw new \invalid_parameter_exception('max_points must be between 0 and 10000.');
        }
        $grade = $params['max_points'];
        if ($params['scale'] !== null) {
            $grade = -self::scale_id($params['courseid'], $params['scale']);
        }
        $converted = $params['description'] === null ? null : content::from_markdown($params['description']);
        $duedate = self::date($params['due'], 'due');
        $cutoffdate = self::date($params['cutoff'], 'cutoff');

        $existing = modules::find_by_key($params['courseid'], $key);
        if ($existing && $existing->modname !== 'assign') {
            throw new \invalid_parameter_exception("The key '{$key}' belongs to a {$existing->modname} activity, "
                . 'not an assignment. Use another key.');
        }
        if (!$existing) {
            $missing = [];
            if ($params['name'] === null) {
                $missing[] = 'name';
            }
            if (!$types) {
                $missing[] = 'submission_types (onlinetext, file or both)';
            }
            if ($grade === null) {
                $missing[] = 'max_points or scale';
            }
            if ($missing) {
                throw new \invalid_parameter_exception("No assignment with the key '{$key}' exists yet, so this creates "
                    . 'one, and nothing is set silently: give ' . implode(', ', $missing) . '. Nothing was created.');
            }
        }

        return dry_run::run(
            $params['dry_run'],
            $params['courseid'],
            function (bool $dryrun) use ($params, $key, $existing, $types, $grade, $converted, $duedate, $cutoffdate) {
                global $DB;
                if ($existing) {
                    $data = modules::current_data($existing);
                    $before = clone $data;
                } else {
                    $data = self::defaults($params['courseid']);
                    $before = null;
                }
                if ($params['name'] !== null) {
                    $data->name = $params['name'];
                }
                if ($converted !== null) {
                    $data->introeditor = ['text' => $converted['html'], 'format' => FORMAT_HTML, 'itemid' => 0];
                }
                if ($params['visible'] !== null) {
                    $data->visible = (int) $params['visible'];
                }
                if ($types !== null) {
                    foreach (self::SUBMISSION_TYPES as $type) {
                        $data->{"assignsubmission_{$type}_enabled"} = (int) in_array($type, $types, true);
                    }
                }
                if ($grade !== null) {
                    $data->grade = $grade;
                }
                if ($duedate !== null) {
                    $data->duedate = $duedate;
                    if (!empty($data->gradingduedate) && $duedate && $data->gradingduedate < $duedate) {
                        $data->gradingduedate = $duedate + WEEKSECS;
                    }
                }
                if ($cutoffdate !== null) {
                    $data->cutoffdate = $cutoffdate;
                }
                if ($data->cutoffdate && $data->duedate && $data->cutoffdate < $data->duedate) {
                    throw new \invalid_parameter_exception('The cut-off date (' . dates::iso((int) $data->cutoffdate)
                        . ') is before the due date (' . dates::iso((int) $data->duedate) . '). Students could not submit '
                        . 'late at all and would be cut off before the deadline. Move the cut-off date to the due date or '
                        . 'later, or remove it. Nothing was saved.');
                }

                if ($existing) {
                    $data->section = $existing->sectionnum;
                    modules::update($existing, $data);
                    if ($params['section'] !== null && $params['section'] != $existing->sectionnum) {
                        moveto_module($existing, get_fast_modinfo($params['courseid'])->get_section_info($params['section']));
                    }
                    $cmid = $existing->id;
                } else {
                    $data->section = $params['section'] ?? 0;
                    $data->visible = (int) ($params['visible'] ?? true);
                    $data->cmidnumber = $key;
                    $cmid = modules::create($data)->id;
                }

                $cm = get_fast_modinfo($params['courseid'])->get_cm($cmid);
                $result = self::effective($cm);
                if ($dryrun && !$existing) {
                    // The assignment is rolled back, so its ID and URL would only mislead.
                    $result['cmid'] = 0;
                    $result['url'] = '';
                }
                $result['status'] = $existing ? 'updated' : 'created';
                $result['key'] = $key;
                $result['changed'] = [];
                if ($before !== null) {
                    $after = modules::current_data($cm);
                    $result['changed'] = modules::changed($before, $after, ['name', 'intro', 'visible', 'grade',
                        'duedate', 'cutoffdate', 'assignsubmission_onlinetext_enabled', 'assignsubmission_file_enabled']);
                }
                $result['removed_by_cleaning'] = $converted['removed'] ?? [];
                return $result;
            }
        );
    }

    /**
     * Form data for a new assignment: the site's assignment defaults, as the web form would fill them in.
     *
     * @param int $courseid
     * @return \stdClass
     */
    private static function defaults(int $courseid): \stdClass {
        $config = get_config('assign');
        $setting = fn(string $name, $fallback) => isset($config->$name) && $config->$name !== '' ? $config->$name : $fallback;
        $filecfg = get_config('assignsubmission_file');
        return (object) [
            'modulename' => 'assign',
            'course' => $courseid,
            'introeditor' => ['text' => '', 'format' => FORMAT_HTML, 'itemid' => 0],
            'alwaysshowdescription' => 1,
            'submissiondrafts' => (int) $setting('submissiondrafts', 0),
            'requiresubmissionstatement' => (int) $setting('requiresubmissionstatement', 0),
            'sendnotifications' => (int) $setting('sendnotifications', 0),
            'sendlatenotifications' => (int) $setting('sendlatenotifications', 0),
            'sendstudentnotifications' => (int) $setting('sendstudentnotifications', 1),
            'allowsubmissionsfromdate' => 0,
            'duedate' => 0,
            'cutoffdate' => 0,
            'gradingduedate' => 0,
            'grade' => 100,
            'teamsubmission' => (int) $setting('teamsubmission', 0),
            'requireallteammemberssubmit' => (int) $setting('requireallteammemberssubmit', 0),
            'teamsubmissiongroupingid' => 0,
            'blindmarking' => (int) $setting('blindmarking', 0),
            'hidegrader' => (int) $setting('hidegrader', 0),
            'attemptreopenmethod' => $setting('attemptreopenmethod', ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS),
            'maxattempts' => (int) $setting('maxattempts', -1),
            'markingworkflow' => (int) $setting('markingworkflow', 0),
            'markingallocation' => (int) $setting('markingallocation', 0),
            'preventsubmissionnotingroup' => (int) $setting('preventsubmissionnotingroup', 0),
            'completionsubmit' => 0,
            'assignsubmission_onlinetext_enabled' => 0,
            'assignsubmission_onlinetext_wordlimit_enabled' => 0,
            'assignsubmission_onlinetext_wordlimit' => 0,
            'assignsubmission_file_enabled' => 0,
            'assignsubmission_file_maxfiles' => (int) ($filecfg->maxfiles ?? 20),
            'assignsubmission_file_maxsizebytes' => (int) ($filecfg->maxbytes ?? 0),
            'assignsubmission_file_filetypes' => '',
            // Feedback comments stay on so grade_submission can add feedback.
            'assignfeedback_comments_enabled' => 1,
        ];
    }

    /**
     * Every effective setting of a saved assignment.
     *
     * @param \cm_info $cm
     * @return array
     */
    private static function effective(\cm_info $cm): array {
        global $DB;
        $instance = $DB->get_record('assign', ['id' => $cm->instance], '*', MUST_EXIST);
        $plugin = fn(string $subtype, string $type, string $name) => $DB->get_field(
            'assign_plugin_config',
            'value',
            ['assignment' => $instance->id, 'subtype' => $subtype, 'plugin' => $type, 'name' => $name]
        );
        $types = array_values(array_filter(
            self::SUBMISSION_TYPES,
            fn($type) => (bool) $plugin('assignsubmission', $type, 'enabled')
        ));
        $scale = $instance->grade < 0 ? $DB->get_field('scale', 'name', ['id' => -$instance->grade]) : null;
        return [
            'cmid' => (int) $cm->id,
            'url' => $cm->url->out(false),
            'name' => $cm->get_formatted_name(),
            'section' => (int) $cm->sectionnum,
            'visible' => (bool) $cm->visible,
            'submission_types' => $types,
            'max_files' => in_array('file', $types, true) ? (int) $plugin('assignsubmission', 'file', 'maxfilesubmissions') : 0,
            'grading' => [
                'type' => $instance->grade < 0 ? 'scale' : ($instance->grade > 0 ? 'points' : 'none'),
                'max_points' => $instance->grade > 0 ? (float) $instance->grade : null,
                'scale' => $scale === null ? null : format_string($scale),
            ],
            'due' => dates::describe((int) $instance->duedate),
            'cutoff' => dates::describe((int) $instance->cutoffdate),
            'open_from' => dates::describe((int) $instance->allowsubmissionsfromdate),
            'grading_due' => dates::describe((int) $instance->gradingduedate),
            'students_must_click_submit' => (bool) $instance->submissiondrafts,
            'submission_statement_required' => (bool) $instance->requiresubmissionstatement,
            'team_submission' => (bool) $instance->teamsubmission,
            'marking_workflow' => (bool) $instance->markingworkflow,
            'anonymous_submissions' => (bool) $instance->blindmarking,
            'feedback_comments' => (bool) $plugin('assignfeedback', 'comments', 'enabled'),
        ];
    }

    /**
     * Parses a date parameter: null keeps, "none" clears (0), anything else must be ISO 8601.
     *
     * @param string|null $value
     * @param string $param
     * @return int|null
     */
    private static function date(?string $value, string $param): ?int {
        if ($value === null) {
            return null;
        }
        if (strtolower(trim($value)) === self::NONE) {
            return 0;
        }
        return dates::parse($value, $param);
    }

    /**
     * ID of a scale by name, among the course's and the site's scales.
     *
     * @param int $courseid
     * @param string $name
     * @return int
     */
    private static function scale_id(int $courseid, string $name): int {
        global $CFG;
        require_once($CFG->libdir . '/grade/grade_scale.php');
        $scales = array_merge(\grade_scale::fetch_all_local($courseid) ?: [], \grade_scale::fetch_all_global() ?: []);
        $names = [];
        foreach ($scales as $scale) {
            if (\core_text::strtolower(trim($scale->name)) === \core_text::strtolower(trim($name))) {
                return (int) $scale->id;
            }
            $names[] = $scale->name;
        }
        throw new \invalid_parameter_exception("No scale called '{$name}'. Available scales: " . implode(', ', $names) . '.');
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'created or updated (with dry_run: would be)'),
            'dry_run' => new external_value(PARAM_BOOL, 'True if nothing was changed'),
            'dry_run_note' => dry_run::note_returns(),
            'cmid' => new external_value(PARAM_INT, 'Course module ID; 0 when a dry run would create the '
                . 'assignment, because it was not created'),
            'key' => new external_value(PARAM_RAW, 'Key'),
            'url' => new external_value(PARAM_URL, 'Assignment URL'),
            'name' => new external_value(PARAM_TEXT, 'Name'),
            'section' => new external_value(PARAM_INT, 'Section number'),
            'visible' => new external_value(PARAM_BOOL, 'Visible to students'),
            'submission_types' => new external_multiple_structure(
                new external_value(PARAM_ALPHA, 'Type'),
                'Enabled submission types'
            ),
            'max_files' => new external_value(PARAM_INT, 'Maximum number of files, when file submission is on'),
            'grading' => new external_single_structure([
                'type' => new external_value(PARAM_ALPHA, 'points, scale or none'),
                'max_points' => new external_value(PARAM_FLOAT, 'Maximum points', VALUE_REQUIRED, null, NULL_ALLOWED),
                'scale' => new external_value(PARAM_TEXT, 'Scale name', VALUE_REQUIRED, null, NULL_ALLOWED),
            ]),
            'due' => dates::describe_returns('Due date'),
            'cutoff' => dates::describe_returns('Cut-off date: after it nothing can be submitted'),
            'open_from' => dates::describe_returns('Submissions open from'),
            'grading_due' => dates::describe_returns('Reminder date for grading'),
            'students_must_click_submit' => new external_value(PARAM_BOOL, 'Students must press submit (drafts on)'),
            'submission_statement_required' => new external_value(PARAM_BOOL, 'Students must accept a statement'),
            'team_submission' => new external_value(PARAM_BOOL, 'Students submit in groups'),
            'marking_workflow' => new external_value(PARAM_BOOL, 'Grades are released through marking workflow'),
            'anonymous_submissions' => new external_value(PARAM_BOOL, 'Blind marking'),
            'feedback_comments' => new external_value(PARAM_BOOL, 'Feedback comments are enabled'),
            'changed' => new external_multiple_structure(
                new external_value(PARAM_ALPHANUMEXT, 'Field'),
                'Fields that changed on update'
            ),
            'removed_by_cleaning' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'Removed item'),
                'Elements and attributes Moodle\'s HTML cleaning removed from the description; tell the teacher'
            ),
        ]);
    }
}
