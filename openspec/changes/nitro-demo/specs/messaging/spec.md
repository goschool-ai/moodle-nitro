## Purpose

Lets the AI reach students on the teacher's behalf through Moodle's own channels: personal messages to individual students (for example, those missing a submission) and course announcements, always after the teacher has seen and approved the text.

## ADDED Requirements

### Requirement: Message students individually
`message_students` SHALL send a message written in markdown to a set of students of one course, selected by user IDs, by group, or as all students. Each recipient SHALL receive it as a separate one-to-one message from the teacher; recipients MUST NOT see each other. It MUST require `moodle/site:sendmessage` and `moodle/course:viewparticipants` in the course, and is subject to the confirmation protocol of `write-confirmation`.

#### Scenario: Reminder to students missing a submission
- **WHEN** a teacher confirms a message to the three students returned by `list_submissions` with filter `missing`
- **THEN** each of the three receives a separate message from the teacher, and the result reports three delivered

#### Scenario: Recipient not in the course
- **WHEN** a user ID in the recipient list is not enrolled in the course
- **THEN** nothing is sent and the error names that user ID

### Requirement: Respect messaging preferences
Delivery SHALL go through Moodle's messaging system, so recipients' messaging privacy settings and notification preferences apply as for a message sent from the web UI. Recipients who cannot be messaged SHALL be reported, not silently skipped.

#### Scenario: Student blocks messages
- **WHEN** one recipient does not accept messages from the teacher
- **THEN** the others receive the message and the result lists that recipient as not delivered with the reason

### Requirement: Post announcement
`post_announcement` SHALL post a discussion with a subject and a markdown message to the course's announcements forum, creating that forum if the course has none, so that Moodle's usual forum notifications go out. It MUST require `mod/forum:addnews` and is subject to the confirmation protocol of `write-confirmation`.

#### Scenario: Announcement
- **WHEN** a teacher confirms an announcement "Bring your laptop on Monday"
- **THEN** a discussion appears in the course's announcements forum, and the result reports how many users are subscribed and will be notified

#### Scenario: Hidden course
- **WHEN** the course is hidden from students at the time of the preview
- **THEN** the preview warns that students cannot see the course and will not be able to open the announcement, so the teacher can make the course visible first
