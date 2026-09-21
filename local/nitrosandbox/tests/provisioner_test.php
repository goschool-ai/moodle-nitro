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
 * Tests for sandbox onboarding: template, provisioning, isolation, fictitious students.
 *
 * @package    local_nitrosandbox
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(provisioner::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(template::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(observer::class)]
final class provisioner_test extends \advanced_testcase {
    /**
     * Builds and saves the template as admin.
     */
    private function template(): void {
        set_config('active', 1, 'local_nitro');
        $this->setAdminUser();
        $courseid = template::build(1);
        template::save_backup($courseid);
        $this->setUser(null);
    }

    /**
     * A new teacher account.
     *
     * @return \stdClass
     */
    private function teacher(): \stdClass {
        return $this->getDataGenerator()->create_user(['auth' => 'email', 'confirmed' => 1]);
    }

    public function test_template_restores_cleanly_into_a_demo_course(): void {
        global $DB;
        $this->resetAfterTest();
        $this->template();
        $teacher = $this->teacher();

        $courseid = provisioner::provision($teacher->id);

        $course = get_course($courseid);
        $this->assertSame('Demo 01 – Haladó programozás', $course->fullname);
        $this->assertEquals(1, $course->visible);
        $context = \context_course::instance($courseid);
        $this->assertTrue(is_enrolled($context, $teacher->id, 'moodle/course:manageactivities'));
        $this->assertTrue(has_capability('local/nitro:use', $context, $teacher->id));
        $modinfo = get_fast_modinfo($courseid);
        $keys = array_map(fn($cm) => $cm->idnumber, $modinfo->get_cms());
        foreach (['requirements', 'week4', 'hw2'] as $key) {
            $this->assertContains($key, $keys);
        }
        $this->assertCount(1, $modinfo->get_instances_of('qbank'));
        $this->assertSame($courseid, (int) get_user_preferences(provisioner::PREFERENCE, 0, $teacher->id));
    }

    public function test_fictitious_students_and_submissions(): void {
        global $DB;
        $this->resetAfterTest();
        $this->template();
        $teacher = $this->teacher();
        $courseid = provisioner::provision($teacher->id);
        $context = \context_course::instance($courseid);

        $students = get_role_users($DB->get_field('role', 'id', ['shortname' => 'student']), $context, false, 'u.*');
        $this->assertCount(20, $students);
        foreach ($students as $student) {
            $this->assertSame('nologin', $student->auth);
            $this->assertStringEndsWith('@example.invalid', $student->email);
        }
        $this->assertCount(2, groups_get_all_groups($courseid));

        $this->setUser($teacher);
        $cmid = $DB->get_field('course_modules', 'id', ['course' => $courseid, 'idnumber' => 'hw2']);
        $call = fn($filter) => \local_nitro\external\list_submissions::execute($cmid, $filter, true, 0);
        $this->assertCount(4, $call('missing')['students']);
        $this->assertCount(4, $call('late')['students']);
        $graded = $call('graded')['students'];
        $this->assertCount(2, $graded);
        $this->assertStringStartsWith('https://git.example.org/', $graded[0]['links'][0]);
        $never = \local_nitro\external\list_participants::execute($courseid, 'student', 0, true, false)['participants'];
        $this->assertCount(3, $never);
    }

    public function test_two_attendees_are_isolated(): void {
        global $DB;
        $this->resetAfterTest();
        $this->template();
        $first = provisioner::provision($this->teacher()->id);
        $second = provisioner::provision($this->teacher()->id);

        $this->assertNotSame($first, $second);
        $this->assertSame('Demo 02 – Haladó programozás', get_course($second)->fullname);
        $studentrole = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $a = array_keys(get_role_users($studentrole, \context_course::instance($first)));
        $b = array_keys(get_role_users($studentrole, \context_course::instance($second)));
        $this->assertSame([], array_intersect($a, $b));
        foreach ($a as $studentid) {
            $this->assertCount(1, enrol_get_all_users_courses($studentid));
        }
    }

    public function test_fictitious_student_cannot_log_in_and_gets_no_email(): void {
        global $DB;
        $this->resetAfterTest();
        $this->template();
        $teacher = $this->teacher();
        $courseid = provisioner::provision($teacher->id);
        $students = get_role_users($DB->get_field('role', 'id', ['shortname' => 'student']),
            \context_course::instance($courseid), false, 'u.*');
        $student = reset($students);

        $failure = null;
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $this->assertFalse(authenticate_user_login($student->username, 'anything', false, $failure));
        $this->assertStringEndsWith('@example.invalid', $student->email);

        $this->setUser($teacher);
        $emails = $this->redirectEmails();
        $messages = $this->redirectMessages();
        $preview = \local_nitro\external\message_students::execute($courseid, 'Szia!', [$student->id], 0, false, '');
        \local_nitro\external\message_students::execute(
            $courseid,
            'Szia!',
            [$student->id],
            0,
            false,
            $preview['confirmation_token']
        );
        $this->assertCount(1, $messages->get_messages());
        $this->assertCount(0, $emails->get_messages());
    }

    public function test_observer_queues_once_for_confirmed_users(): void {
        $this->resetAfterTest();
        set_config('enabled', 1, 'local_nitrosandbox');
        set_config('onboardfrom', time() - MINSECS, 'local_nitrosandbox');
        $confirmed = $this->getDataGenerator()->create_user(['auth' => 'email', 'confirmed' => 1]);
        $unconfirmed = $this->getDataGenerator()->create_user(['auth' => 'email', 'confirmed' => 0]);
        $queued = fn() => count(\core\task\manager::get_adhoc_tasks(task\provision_course::class));

        $this->assertSame(1, $queued());
        \core\event\user_loggedin::create(['userid' => $confirmed->id, 'objectid' => $confirmed->id,
            'other' => ['username' => $confirmed->username]])->trigger();
        \core\event\user_loggedin::create(['userid' => $unconfirmed->id, 'objectid' => $unconfirmed->id,
            'other' => ['username' => $unconfirmed->username]])->trigger();
        $this->assertSame(1, $queued());
    }

    public function test_users_created_before_onboarding_get_nothing(): void {
        $this->resetAfterTest();
        // Onboarding switched on now: accounts that existed before it keep the site as it is.
        set_config('enabled', 1, 'local_nitrosandbox');
        set_config('onboardfrom', time(), 'local_nitrosandbox');
        $old = $this->getDataGenerator()->create_user([
            'auth' => 'email',
            'confirmed' => 1,
            'timecreated' => time() - WEEKSECS,
        ]);
        \core\event\user_loggedin::create([
            'userid' => $old->id,
            'objectid' => $old->id,
            'other' => ['username' => $old->username],
        ])->trigger();

        $this->assertCount(0, \core\task\manager::get_adhoc_tasks(task\provision_course::class));
        $this->assertNull(get_user_preferences(provisioner::PREFERENCE, null, $old->id));
    }

    public function test_deleted_user_gets_nothing(): void {
        global $DB;
        $this->resetAfterTest();
        $this->template();
        $teacher = $this->teacher();
        delete_user($DB->get_record('user', ['id' => $teacher->id]));

        $before = $DB->count_records('course');
        $task = new task\provision_course();
        $task->set_custom_data(['userid' => $teacher->id]);
        $this->expectOutputRegex('/is gone/');
        $task->execute();

        $this->assertSame($before, $DB->count_records('course'));
    }

    public function test_missing_template(): void {
        $this->resetAfterTest();
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessageMatches('/build_template\.php/');
        provisioner::provision($this->teacher()->id);
    }
}
