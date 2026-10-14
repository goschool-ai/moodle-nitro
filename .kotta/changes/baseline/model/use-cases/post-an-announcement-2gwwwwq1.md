---
id: UC-01m4v5sxzyxjm0ekr72gwwwwq1
form: use-case
title: "Post an announcement"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btga1g4n5spb0wkxwx
  - G-01m4v5j7btnqwnk44eqh7yqv9f
refines:
  - BR-01m4v5j7btbvevrsab4mp6a1py
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
  - BR-01m4v5j7bt18sj6vqww7fed3kb
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D11. Grades and announcements through Moodle's own APIs"
  quote: "post_announcement uses forum_get_course_forum($courseid, 'news') (which creates the announcements forum when missing)"
  inferred: null
---
# Post an announcement

## Intent

The teacher posts an announcement to the course news forum after approving its exact text.

## Preconditions

The assistant is connected; the teacher can post to the course's announcements forum.

## Main success scenario

1. The teacher asks for an announcement.
2. The first `post_announcement` call returns a preview and a confirmation token.
3. The teacher approves; the repeated call posts it, and Moodle's usual forum notifications go out by cron.

## Alternatives

- The course has no announcements forum yet: Moodle creates it.
- The teacher asks for a change: a new preview.
