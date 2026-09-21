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

use core_question\local\bank\question_bank_helper;
use local_nitro\external\save_assignment;
use local_nitro\external\save_page;

/**
 * The template demo course: week-4 material, an assignment with a soft and a hard deadline, and an
 * empty shared question bank. Built with nitro's own tools, then saved as a backup without user data.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template {
    /** @var string Short name of the template course. */
    public const SHORTNAME = 'nitro-demo-template';

    /**
     * Builds (or rebuilds) the template course as the current user (an admin).
     *
     * @param int $categoryid
     * @return int course ID
     */
    public static function build(int $categoryid): int {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        if ($old = $DB->get_record('course', ['shortname' => self::SHORTNAME])) {
            delete_course($old, false);
        }
        $course = create_course((object) [
            'fullname' => 'nitro demo sablon – Haladó programozás',
            'shortname' => self::SHORTNAME,
            'category' => $categoryid,
            'visible' => 0,
            'format' => 'topics',
            'numsections' => 4,
            'startdate' => usergetmidnight(time()),
            'summary' => 'A nitro bemutató kurzusának sablonja. Minden regisztráló tanár ennek egy saját példányát kapja.',
            'summaryformat' => FORMAT_HTML,
        ]);
        $names = [1 => '1. hét – Bevezetés', 2 => '2. hét – Függvények', 3 => '3. hét – Adatszerkezetek',
            4 => '4. hét – Rekurzió'];
        foreach ($names as $number => $name) {
            $DB->set_field('course_sections', 'name', $name, ['course' => $course->id, 'section' => $number]);
        }
        rebuild_course_cache($course->id, true);

        save_page::execute($course->id, 'requirements', 'Követelmények', self::REQUIREMENTS, 0, true);
        save_page::execute($course->id, 'week4', 'Rekurzió – 4. heti anyag', self::WEEK4, 4, true);
        save_assignment::execute(
            $course->id,
            'hw2',
            '2. beadandó – Rekurzív bejárás',
            self::HW2,
            4,
            true,
            ['onlinetext'],
            10,
            null,
            date('Y-m-d', strtotime('next friday')) . 'T23:59',
            date('Y-m-d', strtotime('next friday') + 2 * DAYSECS) . 'T20:00'
        );
        question_bank_helper::get_default_open_instance_system_type($course, true);
        return (int) $course->id;
    }

    /**
     * Saves a course as the template backup (without user data).
     *
     * @param int $courseid
     * @return \stored_file
     */
    public static function save_backup(int $courseid): \stored_file {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        $controller = new \backup_controller(
            \backup::TYPE_1COURSE,
            $courseid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            $USER->id
        );
        foreach (
            ['users' => 0, 'anonymize' => 0, 'role_assignments' => 0, 'comments' => 0, 'logs' => 0,
                'grade_histories' => 0, 'badges' => 0] as $name => $value
        ) {
            if ($controller->get_plan()->setting_exists($name)) {
                $controller->get_plan()->get_setting($name)->set_value($value);
            }
        }
        $controller->execute_plan();
        $backup = $controller->get_results()['backup_destination'];
        $controller->destroy();

        $fs = get_file_storage();
        $context = \context_system::instance();
        $fs->delete_area_files($context->id, 'local_nitrosandbox', 'template');
        $file = $fs->create_file_from_storedfile([
            'contextid' => $context->id,
            'component' => 'local_nitrosandbox',
            'filearea' => 'template',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'nitro-demo-template.mbz',
        ], $backup);
        $backup->delete();
        set_config('template', '/nitro-demo-template.mbz', 'local_nitrosandbox');
        return $file;
    }

    /** @var string Requirements page. */
    private const REQUIREMENTS = <<<'MD'
        # Követelmények

        - A félév során **3 beadandót** kell leadni, mindegyiket a határidőig.
        - A beadandókat Moodle-ben kell beadni, a megoldást tartalmazó git repó linkjével.
        - Késve (a határidő után, de a végső határidő előtt) beadott munka legfeljebb **fél pontot** ér.
        - A végső határidő után nem lehet beadni.

        | Beadandó | Pont |
        |----------|------|
        | 1. beadandó | 10 |
        | 2. beadandó | 10 |
        | 3. beadandó | 20 |
        MD;

    /** @var string Week 4 material. */
    private const WEEK4 = <<<'MD'
        # Rekurzió

        Egy függvény **rekurzív**, ha önmagát hívja. Minden rekurzióhoz kell:

        1. egy **alapeset**, amely nem hív tovább, és
        2. egy **rekurzív lépés**, amely kisebb feladatra vezeti vissza az eredetit.

        ## Példa: faktoriális

        ```python
        def fakt(n):
            if n == 0:
                return 1
            return n * fakt(n - 1)
        ```

        ## Visszalépéses keresés

        A visszalépéses keresés (backtracking) részmegoldásokat bővít, és ha zsákutcába jut, visszalép.
        Tipikus feladatok: N királynő, labirintus, részhalmaz-összeg.
        MD;

    /** @var string Assignment description. */
    private const HW2 = <<<'MD'
        Írj rekurzív függvényt, amely bejár egy könyvtárszerkezetet, és kiírja a fájlokat méret szerint csökkenő sorrendben.

        - A megoldást git repóban add be; a Moodle-be a repó linkjét másold be.
        - **Határidő:** péntek 23:59. Utána vasárnap 20:00-ig még beadható, de legfeljebb fél pontot ér.
        MD;
}
