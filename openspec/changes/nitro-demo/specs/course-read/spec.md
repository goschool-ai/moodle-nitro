## Purpose

Gives the AI the read access a teacher needs to answer the daily question "where does my course stand?": which courses they teach, what is in them, who takes them and whether they ever showed up, and who has submitted what.

## ADDED Requirements

### Requirement: List the teacher's courses
`list_courses` SHALL return the courses in which the user holds `local/nitro:use` and can manage or grade content, including courses hidden from students, with course ID, short name, full name, visibility and start and end dates.

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

#### Scenario: Late submission
- **WHEN** a student submitted after the due date without an extension
- **THEN** their entry is marked late
