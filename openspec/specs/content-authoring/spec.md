# content-authoring Specification

## Purpose

Lets the AI keep course material in Moodle in sync with the teacher's own source (for example, markdown in a repository): pages and assignments are created or updated by a stable key, with the same validation as the web UI, and nothing is changed or dropped silently.

## Requirements

### Requirement: Markdown input with a cleaning report
Content tools SHALL accept text in markdown, convert it to HTML with Moodle's own markdown conversion, and store it after Moodle's normal text cleaning. When cleaning removes elements or attributes, the result SHALL list what was removed.

#### Scenario: Headings and lists
- **WHEN** a page is saved with markdown containing headings, a table and a bulleted list
- **THEN** the stored content contains the corresponding HTML elements

#### Scenario: Removed content is reported
- **WHEN** the markdown contains an inline `<svg>` or `<script>` element that Moodle's cleaning removes
- **THEN** the content is saved without it, and the result names the removed element so the teacher can choose another form

### Requirement: Create or update by key
`save_page` and `save_assignment` SHALL take a `key` that identifies the activity within the course (stored as the activity's ID number). When no activity with that key exists, the tool SHALL create one; when it exists, the tool SHALL update it. The result SHALL state whether it created or updated, and return the module ID, key and URL. Calling the tool twice with the same arguments MUST NOT create a duplicate.

#### Scenario: First publish
- **WHEN** a teacher saves a page with key `requirements` that does not exist yet
- **THEN** a page is created in the given section and the result says `created`

#### Scenario: Republish after editing the source
- **WHEN** the teacher saves the page with key `requirements` again with new content
- **THEN** the existing page's content is replaced, no second page appears, and the result says `updated`

### Requirement: Partial updates
On update, only the fields given in the call SHALL change; every other setting (name, section, visibility, dates, grading) MUST stay as it is.

#### Scenario: Description-only update
- **WHEN** a teacher updates an existing assignment's description only
- **THEN** its name, due date, cut-off date, visibility and grading settings are unchanged

### Requirement: Save page
`save_page` SHALL create or update a Page activity with a title, markdown content, section and visibility. It MUST require `moodle/course:manageactivities`, and `mod/page:addinstance` when creating.

#### Scenario: Nonexistent section
- **WHEN** the given section number does not exist in the course
- **THEN** nothing is saved and the error names the highest existing section number

### Requirement: Save assignment without silent defaults
`save_assignment` SHALL create or update an Assignment activity with a name, markdown description, section, visibility, submission types (online text, file upload), grading (maximum points or a scale), due date and cut-off date. When creating, submission types and grading MUST be given explicitly. The result SHALL echo every effective setting, including the ones taken from site defaults. It MUST require `moodle/course:manageactivities`, and `mod/assign:addinstance` when creating.

#### Scenario: Missing submission type
- **WHEN** a teacher creates an assignment without submission types
- **THEN** nothing is created and the error says submission types must be given

#### Scenario: Soft and hard deadline
- **WHEN** a teacher creates an assignment with due date Friday 23:59 and cut-off date Sunday 20:00
- **THEN** students can submit late until Sunday 20:00, and the result shows both dates

#### Scenario: Cut-off before due date
- **WHEN** the cut-off date is earlier than the due date
- **THEN** nothing is saved and the error explains the conflict

### Requirement: Dates are unambiguous
Tools that take dates SHALL accept ISO 8601 date-times and SHALL interpret values without a time zone in the user's Moodle time zone. Results SHALL echo dates in ISO 8601 with offset, together with the weekday.

#### Scenario: Date without offset
- **WHEN** a due date is given as `2026-10-09T23:59`
- **THEN** it is stored as 23:59 in the user's Moodle time zone, and the result shows `2026-10-09T23:59:00+02:00` with the weekday Friday
