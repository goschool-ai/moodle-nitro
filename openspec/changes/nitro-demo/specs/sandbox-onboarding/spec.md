## Purpose

Lets a teacher who saw the demo try nitro on the sandbox on their own the next day: signing up gives them a personal, realistic course with fictitious students, isolated from every other attendee. Sandbox only, never part of what faculties install.

## ADDED Requirements

### Requirement: Independent of the nitro plugin
Sandbox onboarding SHALL be installable and removable on its own. `local_nitro` MUST NOT depend on it, and faculty installations SHALL NOT need it.

#### Scenario: Faculty install
- **WHEN** a faculty admin installs `local_nitro` without the sandbox onboarding component
- **THEN** every `local_nitro` feature works

### Requirement: Personal demo course on signup
When a new user completes signup on the sandbox, a personal copy of the demo course SHALL be created within one minute, named after a neutral sequence number, with the user enrolled as editing teacher and holding `local/nitro:use` in that course.

#### Scenario: Teacher signs up
- **WHEN** a teacher confirms their sandbox account
- **THEN** within one minute they are the editing teacher of a new course "Demo NN" containing the week-4 material, an assignment and an empty course question bank, and they can connect an AI client and call `list_courses` to see it

#### Scenario: Two attendees
- **WHEN** two teachers sign up
- **THEN** each has their own course, and neither is enrolled in or can see the other's course

### Requirement: Isolated fictitious students
Each personal course SHALL have its own 20 fictitious students, created for that course only, with submissions that include on-time, late and missing cases. Fictitious students MUST NOT be able to log in and MUST NOT have a deliverable email address.

#### Scenario: Students do not overlap
- **WHEN** a teacher lists the participants of their demo course
- **THEN** none of the students is enrolled in any other attendee's course, so no student profile reveals another attendee's course

#### Scenario: Submissions to work with
- **WHEN** a teacher calls `list_submissions` with filter `missing` on the demo assignment after its due date
- **THEN** a non-empty list is returned, and filter `late` also returns students

#### Scenario: Messaging a fictitious student
- **WHEN** a teacher sends a message to fictitious students through `message_students`
- **THEN** the message is stored in Moodle and no email leaves the sandbox

#### Scenario: Fictitious student login
- **WHEN** someone tries to log in as a fictitious student
- **THEN** the login is refused

### Requirement: GoSchool-branded sandbox
The sandbox SHALL look like part of the GoSchool brand world, following the parent GoSchool design system (palette, typography, logo): the site name, logo, compact logo, favicon, brand colour and login page SHALL be GoSchool's. Branding SHALL be applied to the sandbox only, through theme settings or a sandbox-only theme, and MUST NOT be part of `local_nitro` or change how nitro looks on a faculty Moodle.

#### Scenario: Attendee opens the sandbox
- **WHEN** an attendee opens the sandbox login page or their demo course
- **THEN** they see the GoSchool logo, colours and site name, not the default Moodle look

#### Scenario: Faculty install
- **WHEN** a faculty admin installs `local_nitro`
- **THEN** their site's theme and branding are unchanged
