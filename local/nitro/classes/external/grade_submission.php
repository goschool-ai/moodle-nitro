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
use local_nitro\local\confirmation;
use local_nitro\local\content;

/**
 * Tool grade_submission: grades and feedback comments through the assign API, after an approved preview.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_submission extends external_api {
    /** @var string[] Marking workflow states a grade can be saved in. */
    private const WORKFLOW_STATES = ['notmarked', 'inmarking', 'readyforreview', 'inreview', 'readyforrelease', 'released'];

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID of the assignment'),
            'grades' => new external_multiple_structure(new external_single_structure([
                'userid' => new external_value(PARAM_INT, 'Student user ID'),
                'grade' => new external_value(PARAM_RAW, 'Points (for example "5" or "7.5") within the maximum, or the '
                    . 'name of a scale item (for example "Complete") on a scale-graded assignment'),
                'feedback' => new external_value(PARAM_RAW, 'Optional feedback comment in markdown', VALUE_DEFAULT, ''),
            ]), 'One entry per student'),
            'workflow_state' => new external_value(
                PARAM_ALPHA,
                'Only with marking workflow: the state to save the grades '
                . 'in (default readyforrelease: graded, not visible to students; released makes them visible)',
                VALUE_DEFAULT,
                ''
            ),
            'confirmation_token' => confirmation::param(),
        ]);
    }

    /**
     * Previews or saves the grades.
     *
     * @param int $cmid
     * @param array $grades
     * @param string $workflowstate
     * @param string $confirmationtoken
     * @return array
     */
    public static function execute(int $cmid, array $grades, string $workflowstate = '', string $confirmationtoken = ''): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->libdir . '/gradelib.php');
        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid, 'grades' => $grades, 'workflow_state' => $workflowstate,
            'confirmation_token' => $confirmationtoken,
        ]);
        $context = access::require_module($params['cmid']);
        require_capability('mod/assign:grade', $context);
        [$course, $cm] = get_course_and_cm_from_cmid($params['cmid'], 'assign');
        $assign = new \assign($context, $cm, $course);
        $instance = $assign->get_instance();
        if (!$params['grades']) {
            throw new \invalid_parameter_exception('grades must contain at least one student.');
        }

        // Validate everything first: nothing is saved unless every entry is valid.
        $scaleitems = self::scale_items($instance);
        $workflow = (bool) $instance->markingworkflow;
        $state = $params['workflow_state'] ?: ($workflow ? 'readyforrelease' : '');
        if ($state !== '' && (!$workflow || !in_array($state, self::WORKFLOW_STATES, true))) {
            throw new \invalid_parameter_exception($workflow
                ? 'workflow_state must be one of ' . implode(', ', self::WORKFLOW_STATES) . '.'
                : 'This assignment does not use marking workflow; leave workflow_state empty.');
        }
        $comments = $assign->get_feedback_plugin_by_type('comments');
        // Asking list_participants() for ids only returns bare id objects, which fullname() renders empty.
        $participants = $assign->list_participants(0, true);
        $visible = access::visible_users((int) $course->id, get_fast_modinfo($course)->get_cm($cm->id), 0);
        if ($visible !== null) {
            $participants = array_intersect_key($participants, array_flip($visible));
        }
        $entries = [];
        foreach ($params['grades'] as $entry) {
            if (!isset($participants[$entry['userid']])) {
                throw new \invalid_parameter_exception("User {$entry['userid']} is not a student of this assignment. "
                    . 'Nothing was saved.');
            }
            if (trim($entry['feedback']) !== '' && !($comments && $comments->is_enabled())) {
                throw new \invalid_parameter_exception('Feedback comments are switched off for this assignment. Save the '
                    . 'grades without feedback, or enable feedback comments in the assignment settings. Nothing was saved.');
            }
            $entries[] = $entry + ['value' => self::grade_value($entry['grade'], $instance, $scaleitems)];
        }

        $visible = !$workflow || $state === 'released';
        $notify = $visible && (bool) $instance->sendstudentnotifications;
        $namefields = implode(', ', array_merge(['id'], \core_user\fields::get_name_fields()));
        $names = $DB->get_records_list('user', 'id', array_column($entries, 'userid'), '', $namefields);
        $students = [];
        foreach ($entries as $entry) {
            $user = $names[$entry['userid']] ?? $participants[$entry['userid']];
            $current = $assign->get_user_grade($entry['userid'], false);
            $hascurrent = $current && $current->grade !== null && (float) $current->grade >= 0;
            $feedback = trim($entry['feedback']) === '' ? null : content::from_markdown($entry['feedback']);
            $students[] = [
                'userid' => (int) $entry['userid'],
                'fullname' => fullname($user),
                'current_grade' => $hascurrent ? self::display($assign, $current->grade, $entry['userid']) : null,
                'new_grade' => self::display($assign, $entry['value'], $entry['userid']),
                'feedback' => $feedback === null ? '' : trim(html_to_text($feedback['html'], 0, false)),
            ];
        }
        $base = [
            'assignment' => format_string($instance->name, true, ['context' => $context]),
            'students' => $students,
            'visible_to_students' => $visible,
            'students_notified' => $notify,
            'visibility_note' => $workflow && !$visible
                ? "Marking workflow is on: the grades are saved as {$state} and students do not see them until they are released."
                : 'Students see the grades as soon as they are saved' . ($notify ? ' and are notified.' : '.'),
        ];

        if ($params['confirmation_token'] === '') {
            return $base + confirmation::result_fields(confirmation::issue('grade_submission', $params))
                + ['affected_ids' => []];
        }

        confirmation::redeem('grade_submission', $params);
        foreach ($entries as $entry) {
            $data = (object) [
                'grade' => $entry['value'],
                'attemptnumber' => -1,
                'sendstudentnotifications' => (int) $notify,
                'applytoall' => 0,
            ];
            if ($workflow) {
                $data->workflowstate = $state;
            }
            if ($comments && $comments->is_enabled()) {
                // The comments plugin always reads its editor; without new feedback, keep the current comment,
                // as the grading form would, instead of wiping it.
                $data->assignfeedbackcomments_editor = trim($entry['feedback']) !== ''
                    ? ['text' => content::from_markdown($entry['feedback'])['html'], 'format' => FORMAT_HTML]
                    : self::current_comment($comments, $assign->get_user_grade($entry['userid'], false));
            }
            $assign->save_grade($entry['userid'], $data);
        }
        $saved = [];
        foreach ($base['students'] as $student) {
            $grade = $assign->get_user_grade($student['userid'], false);
            $saved[] = ['current_grade' => self::display($assign, $grade->grade, $student['userid'])] + $student;
        }
        $base['students'] = $saved;
        return $base + confirmation::result_fields(null) + ['affected_ids' => array_column($saved, 'userid')];
    }

    /**
     * Scale items of a scale-graded assignment, keyed by their 1-based grade value; null for points.
     *
     * @param \stdClass $instance
     * @return array|null
     */
    private static function scale_items(\stdClass $instance): ?array {
        global $DB;
        if ($instance->grade >= 0) {
            return null;
        }
        $scale = $DB->get_field('scale', 'scale', ['id' => -$instance->grade], MUST_EXIST);
        $items = array_map('trim', explode(',', $scale));
        return array_combine(range(1, count($items)), $items);
    }

    /**
     * The value to store for a given grade; throws with the valid values if it is not one.
     *
     * @param string $grade
     * @param \stdClass $instance
     * @param array|null $scaleitems
     * @return float
     */
    private static function grade_value(string $grade, \stdClass $instance, ?array $scaleitems): float {
        $grade = trim($grade);
        if ($scaleitems !== null) {
            foreach ($scaleitems as $value => $item) {
                if (\core_text::strtolower($item) === \core_text::strtolower($grade)) {
                    return (float) $value;
                }
            }
            throw new \invalid_parameter_exception("'{$grade}' is not an item of this assignment's scale. Valid values: "
                . implode(', ', $scaleitems) . '. Nothing was saved.');
        }
        if ((float) $instance->grade <= 0) {
            throw new \invalid_parameter_exception('This assignment is not graded. Nothing was saved.');
        }
        $number = str_replace(',', '.', preg_replace('~\s*/\s*[\d.,]+$~', '', $grade));
        if (!is_numeric($number) || (float) $number < 0 || (float) $number > (float) $instance->grade) {
            throw new \invalid_parameter_exception("'{$grade}' is not a valid grade: give a number from 0 to "
                . format_float($instance->grade, -1) . '. Nothing was saved.');
        }
        return (float) $number;
    }

    /**
     * The current feedback comment of a grade, as editor data.
     *
     * @param \assign_feedback_plugin $comments
     * @param \stdClass|false $grade
     * @return array
     */
    private static function current_comment(\assign_feedback_plugin $comments, $grade): array {
        $current = $grade ? $comments->get_feedback_comments($grade->id) : null;
        return $current
            ? ['text' => $current->commenttext, 'format' => $current->commentformat]
            : ['text' => '', 'format' => FORMAT_HTML];
    }

    /**
     * A grade as Moodle shows it.
     *
     * @param \assign $assign
     * @param float|string $grade
     * @param int $userid
     * @return string
     */
    private static function display(\assign $assign, $grade, int $userid): string {
        return trim(html_to_text($assign->display_grade($grade, false, $userid), 0, false));
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(confirmation::result_returns() + [
            'assignment' => new external_value(PARAM_TEXT, 'Assignment name'),
            'students' => new external_multiple_structure(new external_single_structure([
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'fullname' => new external_value(PARAM_TEXT, 'Name'),
                'current_grade' => new external_value(
                    PARAM_RAW,
                    'Grade before (in a preview) or saved (after)',
                    VALUE_REQUIRED,
                    null,
                    NULL_ALLOWED
                ),
                'new_grade' => new external_value(PARAM_RAW, 'The grade being given'),
                'feedback' => new external_value(PARAM_RAW, 'Feedback comment as plain text; empty if none'),
            ])),
            'visible_to_students' => new external_value(PARAM_BOOL, 'Students see the grades once saved'),
            'students_notified' => new external_value(PARAM_BOOL, 'Students get a notification'),
            'visibility_note' => new external_value(PARAM_TEXT, 'What students will see and when; tell the teacher'),
            'affected_ids' => new external_multiple_structure(new external_value(PARAM_INT, 'User ID'), 'For the log'),
        ]);
    }
}
