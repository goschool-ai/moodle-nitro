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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/tool_testcase.php');

/**
 * Tests for list_forum_posts.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(list_forum_posts::class)]
final class list_forum_posts_test extends tool_testcase {
    /**
     * Calls the tool.
     *
     * @param int $cmid
     * @param int $discussions
     * @param int $posts
     * @return array forums keyed by name
     */
    private function call(int $cmid = 0, int $discussions = 10, int $posts = 20): array {
        $result = list_forum_posts::clean_returnvalue(
            list_forum_posts::execute_returns(),
            list_forum_posts::execute($this->course->id, $cmid, $discussions, $posts)
        );
        return array_column($result['forums'], null, 'name');
    }

    public function test_announcements_with_replies(): void {
        $this->setup_course(2);
        $gen = $this->getDataGenerator();
        $forumgen = $gen->get_plugin_generator('mod_forum');
        $news = $gen->create_module('forum', ['course' => $this->course->id, 'type' => 'news', 'name' => 'Közlemények']);
        $chat = $gen->create_module('forum', ['course' => $this->course->id, 'type' => 'general', 'name' => 'Kérdések']);
        $announcement = $forumgen->create_discussion(['course' => $this->course->id, 'forum' => $news->id,
            'userid' => $this->teacher->id, 'name' => 'Hétfőn laptop kell', 'message' => 'Hozzátok a laptopot!']);
        $question = $forumgen->create_discussion(['course' => $this->course->id, 'forum' => $chat->id,
            'userid' => $this->students[0]->id, 'name' => 'Rekurzió', 'message' => 'Mi az alapeset?']);
        $forumgen->create_post(['discussion' => $question->id, 'userid' => $this->teacher->id,
            'parent' => $question->firstpost, 'message' => 'Az, ami nem hív tovább.']);
        $this->setUser($this->teacher);

        $forums = $this->call();

        $this->assertSame(['Közlemények', 'Kérdések'], array_keys($forums));
        $this->assertSame('news', $forums['Közlemények']['type']);
        $first = $forums['Közlemények']['discussions'][0];
        $this->assertSame('Hétfőn laptop kell', $first['subject']);
        $this->assertSame(fullname($this->teacher), $first['author']);
        $this->assertStringContainsString('Hozzátok a laptopot', $first['posts'][0]['text']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T/', $first['created']);

        $thread = $forums['Kérdések']['discussions'][0];
        $this->assertCount(2, $thread['posts']);
        $this->assertSame(fullname($this->students[0]), $thread['posts'][0]['author']);
        $this->assertSame(0, $thread['posts'][0]['replyto']);
        $this->assertSame($thread['posts'][0]['id'], $thread['posts'][1]['replyto']);
        $this->assertStringContainsString('nem hív tovább', $thread['posts'][1]['text']);
        $this->assertSame((int) $announcement->id, $first['id']);
    }

    public function test_one_forum_and_limits(): void {
        $this->setup_course(1);
        $gen = $this->getDataGenerator();
        $forumgen = $gen->get_plugin_generator('mod_forum');
        $forum = $gen->create_module('forum', ['course' => $this->course->id, 'name' => 'Fórum']);
        $gen->create_module('forum', ['course' => $this->course->id, 'type' => 'news', 'name' => 'Közlemények']);
        for ($i = 1; $i <= 3; $i++) {
            $forumgen->create_discussion(['course' => $this->course->id, 'forum' => $forum->id,
                'userid' => $this->teacher->id, 'name' => "Téma {$i}", 'message' => "Szöveg {$i}"]);
        }
        $this->setUser($this->teacher);

        $forums = $this->call($forum->cmid, 2);
        $this->assertSame(['Fórum'], array_keys($forums));
        $this->assertCount(2, $forums['Fórum']['discussions']);
        // Newest first.
        $this->assertSame('Téma 3', $forums['Fórum']['discussions'][0]['subject']);
    }

    public function test_hidden_forum_is_not_returned(): void {
        $this->setup_course(1);
        $gen = $this->getDataGenerator();
        $hidden = $gen->create_module('forum', ['course' => $this->course->id, 'name' => 'Rejtett', 'visible' => 0]);
        $gen->get_plugin_generator('mod_forum')->create_discussion(['course' => $this->course->id,
            'forum' => $hidden->id, 'userid' => $this->teacher->id, 'name' => 'Titok', 'message' => 'x']);
        $student = $gen->create_and_enrol($this->course, 'student');
        $this->grant_gate($student);
        $this->setUser($student);

        $this->assertSame([], $this->call());
        try {
            $this->call($hidden->cmid);
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('not a forum you can read', $e->getMessage());
            $this->assertStringContainsString('no forum you can read', $e->getMessage());
        }
    }

    public function test_wrong_cmid_names_the_forums(): void {
        $this->setup_course(1);
        $gen = $this->getDataGenerator();
        $forum = $gen->create_module('forum', ['course' => $this->course->id, 'name' => 'Közlemények']);
        $page = $gen->create_module('page', ['course' => $this->course->id]);
        $this->setUser($this->teacher);
        try {
            $this->call($page->cmid);
            $this->fail('Expected an error');
        } catch (\invalid_parameter_exception $e) {
            $this->assertStringContainsString('Közlemények (cmid ' . $forum->cmid . ')', $e->getMessage());
        }
    }

    public function test_separate_groups(): void {
        global $DB;
        $this->setup_course(2);
        $gen = $this->getDataGenerator();
        $forum = $gen->create_module('forum', ['course' => $this->course->id, 'name' => 'Csoportfórum',
            'groupmode' => SEPARATEGROUPS]);
        $mine = $gen->create_group(['courseid' => $this->course->id]);
        $other = $gen->create_group(['courseid' => $this->course->id]);
        $teacher = $gen->create_and_enrol($this->course, 'teacher');
        $this->grant_gate($teacher);
        $gen->create_group_member(['groupid' => $mine->id, 'userid' => $teacher->id]);
        $forumgen = $gen->get_plugin_generator('mod_forum');
        $forumgen->create_discussion(['course' => $this->course->id, 'forum' => $forum->id,
            'userid' => $this->teacher->id, 'name' => 'A csoportnak', 'message' => 'x', 'groupid' => $mine->id]);
        $forumgen->create_discussion(['course' => $this->course->id, 'forum' => $forum->id,
            'userid' => $this->teacher->id, 'name' => 'B csoportnak', 'message' => 'y', 'groupid' => $other->id]);
        $this->setUser($teacher);

        $subjects = array_column($this->call()['Csoportfórum']['discussions'], 'subject');
        $this->assertSame(['A csoportnak'], $subjects);
    }

    public function test_bad_limits(): void {
        $this->setup_course(1);
        $this->setUser($this->teacher);
        $this->expectException(\invalid_parameter_exception::class);
        $this->call(0, 0);
    }
}
