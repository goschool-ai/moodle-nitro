---
id: EX-01m4v5j7btd4c0fedkf83jmky6
form: example
title: First call returns preview
subjects:
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students / Scenario: First call returns preview
    - "local/nitro/tests/external/messaging_test.php · test_reminder_to_three_students"
  quote: a teacher calls `message_students` without a confirmation token
  inferred: "Given: read from the messaging test."
---
# First call returns preview

## Given

A teacher holds `local/nitro:use`, `moodle/site:sendmessage` and `moodle/course:viewparticipants` in a course with enrolled students.

## When

a teacher calls `message_students` without a confirmation token

## Then

no message is sent, and the result contains the recipient count, recipient names, the rendered message and a confirmation token, together with an instruction to show the preview to the teacher and ask for explicit approval
