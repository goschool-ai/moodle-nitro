# course-read Specification

## Purpose

Gives the AI the read access a teacher needs to answer the daily question "where does my course stand?": which courses they teach, what is in them, who takes them and whether they ever showed up, and who has submitted what.

## Requirements

### Requirement: List the teacher's courses
`list_courses` SHALL return the courses in which the user holds `local/nitro:use` and can manage or grade content, including courses hidden from students, with course ID, short name, full name, visibility and start and end dates.

#### Scenario: Which Moodle this is
- **WHEN** `list_courses` returns
- **THEN** the result names the site and its address, so an AI connected to more than one Moodle can say which one the courses come from

#### Scenario: Teacher with several courses
- **WHEN** a teacher of courses A and B, who is a student in course C, calls `list_courses`
- **THEN** A and B are returned and C is not

#### Scenario: Course not yet visible
- **WHEN** course A is hidden from students
- **THEN** it is returned and marked as hidden

### Requirement: Course overview
`course_overview` SHALL return, for a course, its sections in order with their activities (ID, key, type, name, visibility, due date where applicable) and the number of enrolled students.

#### Scenario: Overview of a course
- **WHEN** a teacher calls `course_overview` for their course
- **THEN** the result lists every section with its activities and their due dates, and the student count

#### Scenario: Final deadline in the overview
- **WHEN** an assignment has a cut-off date after its due date
- **THEN** the overview lists the cut-off date next to the due date, so whether students can still submit is visible without reading the submissions

#### Scenario: Hidden activities
- **WHEN** the course contains hidden activities
- **THEN** they are included and marked as hidden, as a teacher sees them in the web UI

### Requirement: List participants
`list_participants` SHALL return the enrolled users of a course with their roles, groups and last access to the course (or that they never accessed it), optionally filtered by role or group, following the data minimisation rules of `access-control`. It MUST require `moodle/course:viewparticipants`.

#### Scenario: Filter by role
- **WHEN** a teacher calls `list_participants` with role `student`
- **THEN** only users with the student role are returned

#### Scenario: Students who never showed up
- **WHEN** a teacher asks for students who never accessed the course
- **THEN** only students with no course access are returned

### Requirement: List assignment submissions
`list_submissions` SHALL return, for an assignment, every student's submission status (not submitted, draft, submitted), submission time, whether it was late, any granted extension, whether it has been graded, and the current grade. With `include_content` set, it SHALL also return the online text of the submission (links in it listed separately) and the names and sizes of submitted files. It SHALL support filtering to missing, late, ungraded or graded submissions. It MUST require `mod/assign:grade` in the assignment's context.

#### Scenario: Missing submissions
- **WHEN** a teacher calls `list_submissions` with filter `missing` after the due date
- **THEN** only students without a submitted submission are returned

#### Scenario: Graded students stay in the list
- **WHEN** a submission has been graded
- **THEN** it is still returned as submitted, marked as graded with its grade, so a status check never loses accepted students

#### Scenario: Submitted repository links
- **WHEN** students submitted an online text containing a repository URL and the teacher sets `include_content`
- **THEN** each entry contains the text and the URL listed as a link

#### Scenario: Overdue submission
- **WHEN** a student has submitted nothing and the deadline (with any extension) has passed
- **THEN** their entry is marked overdue

#### Scenario: Late submission
- **WHEN** a student submitted after the due date without an extension
- **THEN** their entry is marked late

### Requirement: Read forum discussions
`list_forum_posts` SHALL return the discussions of a course's forums with their posts: forum name, discussion subject, author, time, and the message as text, newest discussion first. It SHALL take either a course (all forums the user may see) or one forum by course module ID, and SHALL limit how many discussions and posts it returns. It MUST apply the same access rules as the web UI (`mod/forum:viewdiscussion` per forum, group mode, and posts hidden until a user has posted in a Q&A forum are not returned).

#### Scenario: Reading the announcements
- **WHEN** a teacher asks what was announced in their course
- **THEN** the discussions of the announcements forum are returned with subject, author, time and text

#### Scenario: Replies
- **WHEN** a discussion has replies
- **THEN** the replies are returned under their discussion, in order, with their authors

#### Scenario: Forum the user cannot see
- **WHEN** a course contains a forum hidden from the user
- **THEN** its discussions are not returned


### Requirement: Read an activity
`read_activity` SHALL return, for one course module, its type, name, visibility, URL, description and — where the activity carries text of its own (page, book chapter list, label, assignment instructions, quiz or forum intro, url target) — that text, so the AI can quote or rewrite existing content instead of asking the teacher to copy it out of the browser. It SHALL return the text as plain text by default and the original HTML when `include_html` is set, SHALL list the activity's dates where it has them, and SHALL say so in a note when the activity keeps no readable text. It MUST require `local/nitro:use` in the course and the same view access as the web UI.

#### Scenario: Reading a page
- **WHEN** a teacher asks what is on the week 4 page
- **THEN** the page's content is returned as text

#### Scenario: Rewriting existing content
- **WHEN** the AI is asked to extend a page it did not write
- **THEN** it can read the current content with `include_html` and save the edited HTML back with `save_page`

#### Scenario: Assignment instructions
- **WHEN** the module is an assignment
- **THEN** its description and due dates are returned

#### Scenario: Activity with no text
- **WHEN** the module is an uploaded file with no description
- **THEN** the result carries a note that the activity keeps no readable text, instead of an empty answer

#### Scenario: Activity the user cannot see
- **WHEN** a teacher without access to the course calls `read_activity`
- **THEN** the call is refused
