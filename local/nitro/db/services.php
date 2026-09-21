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
 * External functions behind the nitro tools.
 *
 * Descriptions are what the AI reads in tools/list: they say when to use the tool and what it changes.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_nitro_list_courses' => [
        'classname' => \local_nitro\external\list_courses::class,
        'description' => 'List the courses the teacher teaches and can work on through nitro, including courses '
            . 'hidden from students. Start here to find the course ID the other tools need. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_course_overview' => [
        'classname' => \local_nitro\external\course_overview::class,
        'description' => 'Show a course as the teacher sees it: sections in order, their activities with course module '
            . 'ID, stable key, type, visibility and dates (due dates, close dates), and the number of students. '
            . 'Use it to find an assignment\'s cmid or a page\'s key before other tools. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_list_participants' => [
        'classname' => \local_nitro\external\list_participants::class,
        'description' => 'List the people enrolled in a course with their roles, groups and last access to the course '
            . '(null means they never opened it). Filter by role (for example student), group, or never_accessed. '
            . 'Email addresses and ID numbers only with include_identity. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_list_submissions' => [
        'classname' => \local_nitro\external\list_submissions::class,
        'description' => 'Show every student\'s submission for an assignment: submitted or not, when, whether late '
            . '(after the due date and any extension), extension, whether graded and the grade. Graded students stay '
            . 'in the list. Use filter missing, late, ungraded or graded for "who is behind?" questions; set '
            . 'include_content to read the submitted text, its links and file names. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_list_forum_posts' => [
        'classname' => \local_nitro\external\list_forum_posts::class,
        'description' => 'Read forum discussions of a course with their posts: subject, author, time and text, '
            . 'newest discussion first. Use it to see what was announced (the announcements forum has type news) or '
            . 'what students are discussing. Pass a cmid for one forum. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_read_activity' => [
        'classname' => \local_nitro\external\read_activity::class,
        'description' => 'Read the text of one activity: a page\'s content, an assignment\'s or a quiz\'s '
            . 'description, with its dates. Use it before writing questions, a summary or an announcement from the '
            . 'course\'s own material, instead of asking the teacher to paste it. Changes nothing.',
        'type' => 'read',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_save_page' => [
        'classname' => \local_nitro\external\save_page::class,
        'description' => 'Create or update a Page from markdown, identified by a stable key: the same key always '
            . 'updates the same page, so republishing from the teacher\'s source never duplicates it. On update only '
            . 'the fields you give change. Changes the course; use dry_run: true to preview. The result lists anything '
            . 'Moodle\'s HTML cleaning removed; tell the teacher.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_save_assignment' => [
        'classname' => \local_nitro\external\save_assignment::class,
        'description' => 'Create or update an Assignment from markdown, identified by a stable key. When creating, '
            . 'submission_types and max_points (or scale) are required: nothing is set silently. due is when late '
            . 'starts, cutoff is when submission closes. On update only the fields you give change. The result shows '
            . 'every effective setting with weekdays: read the dates back to the teacher. Changes the course; use '
            . 'dry_run: true to preview.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_import_questions' => [
        'classname' => \local_nitro\external\import_questions::class,
        'description' => 'Import questions written in GIFT or Moodle XML into a named category of the course\'s shared '
            . 'question bank (or a quiz\'s own bank), with Moodle\'s own importer. The category is created if missing. '
            . 'All or nothing: on a parse error nothing is imported and the error names the faulty question. Returns '
            . 'the new question IDs. Changes the course; use dry_run: true to check the questions first.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_create_quiz' => [
        'classname' => \local_nitro\external\create_quiz::class,
        'description' => 'Create an empty Quiz: name, introduction, open and close times, time limit, attempts, '
            . 'question shuffling and a review preset (practice: answers shown after each attempt; exam: only after '
            . 'the quiz closes). Hidden by default until questions are added. Then use add_questions_to_quiz. '
            . 'Changes the course; use dry_run: true to preview.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_add_questions_to_quiz' => [
        'classname' => \local_nitro\external\add_questions_to_quiz::class,
        'description' => 'Add questions to a quiz: specific questions by ID, and/or a number of random questions drawn '
            . 'from a category by name, each with a mark; optionally repaginate. Fails without changes if the '
            . 'category holds fewer questions than requested, or if students already attempted the quiz. Changes the '
            . 'course; use dry_run: true to preview.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_message_students' => [
        'classname' => \local_nitro\external\message_students::class,
        'description' => 'Send a personal message (markdown) from the teacher to students of a course: by user IDs, '
            . 'a group, or all students. Each gets a separate one-to-one message. Reaches students, so it needs '
            . 'confirmation: the first call only returns a preview and a confirmation_token; show the preview to the '
            . 'teacher and call again with the token only after they approve.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_post_announcement' => [
        'classname' => \local_nitro\external\post_announcement::class,
        'description' => 'Post an announcement (subject and markdown) to the course\'s announcements forum; Moodle then '
            . 'notifies subscribers. Reaches students, so it needs confirmation: the first call only returns a preview '
            . 'with warnings and a confirmation_token; call again with the token only after the teacher approves.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_grade_submission' => [
        'classname' => \local_nitro\external\grade_submission::class,
        'description' => 'Save grades the teacher decided on for one or more students of an assignment: points within '
            . 'the maximum, or a scale item by name, with an optional feedback comment. Never decide grades yourself. '
            . 'Needs confirmation: the first call returns a preview (current and new grade, whether students see it) '
            . 'and a confirmation_token; call again with the token only after the teacher approves.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
    'local_nitro_send_feedback' => [
        'classname' => \local_nitro\external\send_feedback::class,
        'description' => 'Tell the nitro team what is missing or broken: when a teacher wants something nitro has no '
            . 'tool for, or a tool behaves wrongly, offer to send feedback and call this. It mails the text to the '
            . 'address the site configured, with the site name and the nitro and Moodle versions, and nothing else: '
            . 'no course content, no student data. Needs confirmation: the first call returns the exact message and a '
            . 'confirmation_token; send only after the teacher has read it and approved.',
        'type' => 'write',
        'loginrequired' => true,
        'ajax' => false,
    ],
];
