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

namespace local_nitrosandbox;

/**
 * Builds a personal demo course: the template restored as "Demo NN", with its own fictitious
 * students, groups, submissions and last-access data, and the new user as its editing teacher.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provisioner {
    /** @var string User preference holding the demo course ID (0 while it is being created). */
    public const PREFERENCE = 'local_nitrosandbox_courseid';

    /** @var string Short name of the role that grants local/nitro:use in a demo course. */
    public const ROLE = 'nitrodemo';

    /** @var string Key (ID number) of the demo assignment in the template. */
    public const ASSIGNMENT_KEY = 'hw2';

    /** @var string[] Fictitious students: first name, last name. */
    private const NAMES = [
        ['Bence', 'Kovács'], ['Luca', 'Szabó'], ['Máté', 'Tóth'], ['Hanna', 'Varga'], ['Levente', 'Horváth'],
        ['Zoé', 'Kiss'], ['Dávid', 'Molnár'], ['Anna', 'Németh'], ['Ádám', 'Farkas'], ['Lili', 'Balogh'],
        ['Marcell', 'Papp'], ['Emma', 'Takács'], ['Balázs', 'Juhász'], ['Réka', 'Lakatos'], ['Noel', 'Mészáros'],
        ['Kata', 'Oláh'], ['Gergő', 'Simon'], ['Dóra', 'Rácz'], ['Zsombor', 'Fekete'], ['Petra', 'Szilágyi'],
    ];

    /** @var int[] Students (by index) who submitted nothing. */
    private const MISSING = [4, 9, 14, 19];

    /** @var int[] Students who submitted after the due date. */
    private const LATE = [3, 8, 13, 18];

    /** @var int[] Students who never opened the course. */
    private const NEVER_ACCESSED = [9, 14, 19];

    /** @var int[] Students whose submission is already graded. */
    private const GRADED = [0, 1];

    /** @var int[] Students who submitted on time but forgot the repository link. */
    private const NOLINK = [6, 11];

    /** @var string[] What those students wrote instead of a link. */
    private const NOLINK_TEXTS = [
        '<p>Kész a 2. beadandó, a repót még feltöltöm.</p>',
        '<p>Beadom a 2. házit. A kódot e-mailben küldöm.</p>',
    ];

    /**
     * The role that grants local/nitro:use in a course; created when missing.
     *
     * @return int role ID
     */
    public static function gate_role(): int {
        global $DB;
        $roleid = $DB->get_field('role', 'id', ['shortname' => self::ROLE]);
        if (!$roleid) {
            $roleid = create_role('nitro demo', self::ROLE, 'Allows using Moodle through an AI assistant (nitro) in a '
                . 'sandbox demo course.', '');
            set_role_contextlevels($roleid, [CONTEXT_COURSE]);
            assign_capability('local/nitro:use', CAP_ALLOW, $roleid, \context_system::instance()->id, true);
        }
        return (int) $roleid;
    }

    /**
     * Creates the demo course for a user.
     *
     * @param int $userid
     * @return int course ID
     */
    public static function provision(int $userid): int {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        // The user may be gone by the time the task runs (deleted account, cancelled signup).
        if (!$DB->record_exists('user', ['id' => $userid, 'deleted' => 0])) {
            throw new \moodle_exception('usergone', 'local_nitrosandbox');
        }
        $existing = (int) get_user_preferences(self::PREFERENCE, 0, $userid);
        if ($existing && $DB->record_exists('course', ['id' => $existing])) {
            return $existing;
        }
        $courseid = 0;
        try {
            return self::build($userid, $courseid);
        } catch (\Throwable $e) {
            // Leave nothing half-built: a retry of this task must start clean.
            if ($courseid && $DB->record_exists('course', ['id' => $courseid])) {
                delete_course($courseid, false);
            }
            unset_user_preference(self::PREFERENCE, $userid);
            throw $e;
        }
    }

    /**
     * Builds the course; see provision().
     *
     * @param int $userid
     * @param int $courseid set as soon as the course exists, so a failure can clean it up
     * @return int course ID
     */
    private static function build(int $userid, int &$courseid): int {
        global $CFG, $DB;
        $label = self::reserve_label();
        $courseid = self::restore_template("Demo {$label} – Haladó programozás", "demo{$label}");
        $course = get_course($courseid);
        $context = \context_course::instance($courseid);

        $manual = self::manual_enrol_instance($course);
        $plugin = enrol_get_plugin('manual');
        $studentrole = (int) $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $teacherrole = (int) $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);

        $groups = [
            groups_create_group((object) ['courseid' => $courseid, 'name' => 'A csoport']),
            groups_create_group((object) ['courseid' => $courseid, 'name' => 'B csoport']),
        ];
        $count = min(count(self::NAMES), max(1, (int) (get_config('local_nitrosandbox', 'students') ?: 20)));
        $students = [];
        for ($i = 0; $i < $count; $i++) {
            [$first, $last] = self::NAMES[$i];
            $username = "demo{$courseid}.s" . ($i + 1);
            $id = user_create_user((object) [
                'username' => $username,
                'auth' => 'nologin',
                'confirmed' => 1,
                'mnethostid' => $CFG->mnet_localhost_id,
                'firstname' => $first,
                'lastname' => $last,
                'email' => "{$username}@example.invalid",
                // No usable password: nologin accounts never authenticate.
                'password' => AUTH_PASSWORD_NOT_CACHED,
            ], false, false);
            $plugin->enrol_user($manual, $id, $studentrole);
            groups_add_member($groups[$i % 2], $id);
            $students[$i] = $id;
            if (!in_array($i, self::NEVER_ACCESSED, true)) {
                $DB->insert_record('user_lastaccess', ['userid' => $id, 'courseid' => $courseid,
                    'timeaccess' => time() - random_int(HOURSECS, 6 * DAYSECS)]);
            }
        }

        self::seed_assignment($course, $students);

        $plugin->enrol_user($manual, $userid, $teacherrole);
        role_assign(self::gate_role(), $userid, $context);
        set_user_preference(self::PREFERENCE, $courseid, $userid);
        return $courseid;
    }

    /**
     * Sets the demo assignment's dates around now and creates the students' submissions.
     *
     * The due date has just passed and the cut-off is ahead, so "who is behind?" has answers and late
     * work is still possible.
     *
     * @param \stdClass $course
     * @param array $students index => user ID
     */
    private static function seed_assignment(\stdClass $course, array $students): void {
        global $DB;
        $cmid = $DB->get_field('course_modules', 'id', ['course' => $course->id, 'idnumber' => self::ASSIGNMENT_KEY]);
        if (!$cmid) {
            return;
        }
        [$course, $cm] = get_course_and_cm_from_cmid($cmid, 'assign');
        $due = usergetmidnight(time()) - MINSECS;
        $DB->update_record('assign', ['id' => $cm->instance, 'duedate' => $due, 'cutoffdate' => $due + 3 * DAYSECS,
            'allowsubmissionsfromdate' => $due - 7 * DAYSECS, 'gradingduedate' => $due + 7 * DAYSECS]);
        rebuild_course_cache($course->id, true);
        [$course, $cm] = get_course_and_cm_from_cmid($cmid, 'assign');
        $assign = new \assign(\context_module::instance($cm->id), $cm, $course);
        $assign->update_calendar($cm->id);

        foreach ($students as $i => $userid) {
            if (in_array($i, self::MISSING, true)) {
                continue;
            }
            $time = in_array($i, self::LATE, true) ? $due + random_int(HOURSECS, 20 * HOURSECS)
                : $due - random_int(HOURSECS, 3 * DAYSECS);
            $submission = $assign->get_user_submission($userid, true);
            $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            $submission->timecreated = $time;
            $submission->timemodified = $time;
            $DB->update_record('assign_submission', $submission);
            $username = "demo{$course->id}.s" . ($i + 1);
            $nolink = array_search($i, self::NOLINK, true);
            $DB->insert_record('assignsubmission_onlinetext', [
                'assignment' => $cm->instance,
                'submission' => $submission->id,
                'onlinetext' => $nolink !== false ? self::NOLINK_TEXTS[$nolink]
                    : '<p>Kész a 2. beadandó. Repó: <a href="https://git.example.org/' . $username
                        . '/hazi2">https://git.example.org/' . $username . '/hazi2</a></p>',
                'onlineformat' => FORMAT_HTML,
            ]);
            if (in_array($i, self::GRADED, true)) {
                $grade = $assign->get_user_grade($userid, true);
                $grade->grade = 10;
                $assign->update_grade($grade);
            }
        }
    }

    /**
     * Restores the template backup into a new course.
     *
     * @param string $fullname
     * @param string $shortname
     * @return int course ID
     */
    private static function restore_template(string $fullname, string $shortname): int {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        $files = get_file_storage()->get_area_files(
            \context_system::instance()->id,
            'local_nitrosandbox',
            'template',
            0,
            'id',
            false
        );
        $file = reset($files);
        if (!$file) {
            throw new \moodle_exception('templatemissing', 'local_nitrosandbox');
        }
        $folder = 'nitrosandbox_' . bin2hex(random_bytes(6));
        $file->extract_to_pathname(
            get_file_packer('application/vnd.moodle.backup'),
            make_backup_temp_directory($folder)
        );

        $categoryid = (int) get_config('local_nitrosandbox', 'categoryid') ?: 1;
        $courseid = \restore_dbops::create_new_course($fullname, $shortname, $categoryid);
        $controller = new \restore_controller(
            $folder,
            $courseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            get_admin()->id,
            \backup::TARGET_NEW_COURSE
        );
        $controller->get_plan()->get_setting('users')->set_value(false);
        if (!$controller->execute_precheck()) {
            $results = $controller->get_precheck_results();
            if (!empty($results['errors'])) {
                throw new \moodle_exception('restorefailed', 'local_nitrosandbox', '', implode('; ', $results['errors']));
            }
        }
        $controller->execute_plan();
        $controller->destroy();
        // The restore renames the course to the template's name, so name it again here.
        self::name_course($courseid, substr($shortname, -2));
        return $courseid;
    }

    /**
     * The course's manual enrolment instance, added when missing.
     *
     * @param \stdClass $course
     * @return \stdClass
     */
    private static function manual_enrol_instance(\stdClass $course): \stdClass {
        global $DB;
        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual'], '*', IGNORE_MULTIPLE);
        if (!$instance) {
            $id = enrol_get_plugin('manual')->add_default_instance($course);
            $instance = $DB->get_record('enrol', ['id' => $id], '*', MUST_EXIST);
        }
        return $instance;
    }

    /**
     * Reserves the next free demo label, safe against parallel tasks.
     *
     * The number comes from the courses that exist, not from a counter: a failed run that was cleaned
     * up frees its number again, and a lost counter update cannot hand the same number out twice.
     *
     * @return string two-digit label
     */
    private static function reserve_label(): string {
        global $DB;
        $lock = \core\lock\lock_config::get_lock_factory('local_nitrosandbox')->get_lock('number', 30);
        if (!$lock) {
            throw new \moodle_exception('locktimeout');
        }
        try {
            $used = [];
            foreach ($DB->get_fieldset_select('course', 'shortname', $DB->sql_like('shortname', '?'), ['demo%']) as $name) {
                if (preg_match('/^demo(\d+)$/', $name, $match)) {
                    $used[] = (int) $match[1];
                }
            }
            $number = max(array_merge([0], $used, [(int) get_config('local_nitrosandbox', 'lastnumber')])) + 1;
            set_config('lastnumber', $number, 'local_nitrosandbox');
            return sprintf('%02d', $number);
        } finally {
            $lock->release();
        }
    }

    /**
     * Gives the restored course its demo name, skipping a short name another course already took.
     *
     * @param int $courseid
     * @param string $label
     */
    private static function name_course(int $courseid, string $label): void {
        global $DB;
        $number = (int) $label;
        while ($DB->record_exists_select('course', 'shortname = ? AND id <> ?', ["demo{$label}", $courseid])) {
            $label = sprintf('%02d', ++$number);
        }
        update_course((object) [
            'id' => $courseid,
            'fullname' => "Demo {$label} – Haladó programozás",
            'shortname' => "demo{$label}",
            'visible' => 1,
            'startdate' => usergetmidnight(time()) - 21 * DAYSECS,
            'enddate' => 0,
        ]);
    }
}
