# audit-log Specification

## Purpose

Makes everything the AI did in Moodle visible afterwards, in the log reports admins and teachers already use.

## Requirements

### Requirement: Event for every tool call
Every `tools/call` SHALL trigger a plugin-specific Moodle event recording the acting user, the OAuth client, the tool name, the course (when the tool targets one), the outcome (success, error, preview) and the time. The event SHALL appear in the standard Moodle logs report.

#### Scenario: Successful write
- **WHEN** a teacher creates a page through `save_page`
- **THEN** the course log shows a nitro tool-call event naming the teacher, the client, `save_page` and the course, followed by Moodle's own module-created event

#### Scenario: Denied call
- **WHEN** a call fails on a capability check
- **THEN** a tool-call event with outcome `error` is still recorded

### Requirement: No content in the log
The tool-call event MUST NOT store message bodies, page content or question text; it SHALL store only identifiers of the affected objects.

#### Scenario: Message sent
- **WHEN** `message_students` sends a message
- **THEN** the event records the recipient count and the recipient user IDs, not the message text
