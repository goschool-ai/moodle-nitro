## Purpose

Makes human approval enforceable on the server for everything that reaches students (messages, announcements, grades), so safety does not depend on the AI client following instructions: the teacher always sees the text before it is sent.

## ADDED Requirements

### Requirement: Dry run for every write tool
Every tool that changes Moodle data SHALL accept a `dry_run` flag. With `dry_run` set, the tool MUST change nothing and SHALL return a description of what it would change, including the effective settings that would be stored.

#### Scenario: Dry run of a page update
- **WHEN** a teacher calls `save_page` with `dry_run: true` for an existing key
- **THEN** nothing is changed and the result says the page would be updated, lists the fields that would change, and reports any content Moodle's HTML cleaning would remove

### Requirement: Confirmation for writes that reach students
Tools that notify students or change what students see of their results (in this change: `message_students`, `post_announcement`, `grade_submission`) MUST NOT act on the first call. The first call SHALL return a preview and a confirmation token; the action SHALL be executed only when the tool is called again with the same arguments and that token.

#### Scenario: First call returns preview
- **WHEN** a teacher calls `message_students` without a confirmation token
- **THEN** no message is sent, and the result contains the recipient count, recipient names, the rendered message and a confirmation token, together with an instruction to show the preview to the teacher and ask for explicit approval

#### Scenario: Announcement preview
- **WHEN** a teacher calls `post_announcement` without a confirmation token
- **THEN** nothing is posted, and the result contains the subject, the rendered text, how many users would be notified, and a confirmation token

#### Scenario: Grades preview
- **WHEN** a teacher calls `grade_submission` for several students without a confirmation token
- **THEN** no grade is saved, and the result lists each student with the current and the new grade and any feedback text, and a confirmation token

#### Scenario: Confirmed call executes
- **WHEN** the same user calls the tool again with identical arguments and the returned token within its validity
- **THEN** the action is executed and the result reports what was done

#### Scenario: Arguments changed after preview
- **WHEN** the confirming call's arguments differ from the previewed ones
- **THEN** nothing is executed and the result asks for a new preview

### Requirement: Confirmation token properties
A confirmation token MUST be single-use, bound to the user, the tool and a hash of the arguments, and MUST expire within 10 minutes.

#### Scenario: Token reuse
- **WHEN** a confirmation token that has already been used is presented again
- **THEN** nothing is executed and the result asks for a new preview

#### Scenario: Token of another user
- **WHEN** a user presents a confirmation token issued to a different user
- **THEN** nothing is executed
