---
id: EX-01m4v5j7btn42q9z8c78bstbb4
form: example
title: Announcement
subjects:
  - BR-01m4v5j7btbvevrsab4mp6a1py
  - UC-01m4v5sxzyxjm0ekr72gwwwwq1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Post announcement /
      Scenario: Announcement
    - 'local/nitro/tests/external/messaging_test.php · test_announcement'
  quote: a teacher confirms an announcement "Bring your laptop on Monday"
  inferred: 'Given supplied from the test setup (visible course with three students).'
---
# Announcement

## Given

A visible course has enrolled students, and the teacher may post announcements in it.

## When

a teacher confirms an announcement "Bring your laptop on Monday"

## Then

a discussion appears in the course's announcements forum, and the result reports how many users are subscribed and will be notified
