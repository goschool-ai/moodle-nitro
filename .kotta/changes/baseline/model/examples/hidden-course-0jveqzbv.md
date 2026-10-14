---
id: EX-01m4v5j7bt919v6mj90jveqzbv
form: example
title: Hidden course
subjects:
  - BR-01m4v5j7btbvevrsab4mp6a1py
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Post announcement /
      Scenario: Hidden course
    - 'local/nitro/tests/external/messaging_test.php · test_hidden_course_warning'
  quote: the course is hidden from students at the time of the preview
  inferred: 'Given supplied from the test setup (course with visible = 0).'
---
# Hidden course

## Given

The course is hidden from students, and the teacher may post announcements in it.

## When

the course is hidden from students at the time of the preview

## Then

the preview warns that students cannot see the course and will not be able to open the announcement, so the teacher can make the course visible first
