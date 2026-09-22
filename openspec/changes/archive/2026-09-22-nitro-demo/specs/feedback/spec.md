## Purpose

Lets the teacher (through the AI) tell the nitro team what is missing or broken, from inside the work itself, without leaving Moodle and without the AI sending anything the teacher has not seen.

## ADDED Requirements

### Requirement: Send feedback about nitro
`send_feedback` SHALL send a message written by the AI or the teacher to the address the admin configured (by default the nitro team's address), through Moodle's own mail. It SHALL carry the text, the site's name and URL, the nitro version, the Moodle version, and optionally the tool the feedback is about; it MUST NOT carry course content, student names or student data. The site admin SHALL be able to change the address or switch the tool off.

#### Scenario: A missing tool
- **WHEN** the AI hits a task nitro has no tool for and the teacher approves sending feedback about it
- **THEN** the message arrives at the configured address with the text, the site and the nitro version

#### Scenario: The mail server is not reachable
- **WHEN** the mail server refuses or cannot be resolved at the moment the teacher approves the message
- **THEN** the result says the message is queued rather than sent, and Moodle keeps retrying it, so the report is never silently lost

#### Scenario: No address configured
- **WHEN** the site has no valid feedback address
- **THEN** the call returns an error saying so, instead of pretending to send

#### Scenario: Switched off
- **WHEN** the admin switches feedback off
- **THEN** the tool is not listed and calling it returns an error

### Requirement: The teacher sees the feedback before it is sent
`send_feedback` is subject to the confirmation protocol of `write-confirmation`: the first call SHALL return the exact message and a confirmation token, and it SHALL be sent only when the call is repeated with that token.

#### Scenario: Preview first
- **WHEN** the AI calls `send_feedback` without a confirmation token
- **THEN** nothing is sent and the result contains the message that would be sent, the recipient address and a confirmation token

#### Scenario: Teacher rewrites it
- **WHEN** the teacher changes the text and the AI calls again with the old token
- **THEN** nothing is sent and the result asks for a new preview
