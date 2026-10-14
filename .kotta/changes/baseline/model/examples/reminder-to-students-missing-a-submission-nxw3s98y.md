---
id: EX-01m4v5j7bte4q93417nxw3s98y
form: example
title: Reminder to students missing a submission
subjects:
  - BR-01m4v5j7bt6b8710qcmzqc20pj
  - UC-01m4v5sw613zsg92tcczb36t32
  - UC-01m4v5sxq7mrw8dcspk5n11dd7
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Message students
      individually / Scenario: Reminder to students missing a submission
    - 'local/nitro/tests/external/messaging_test.php · test_reminder_to_three_students'
  quote: >-
    a teacher confirms a message to the three students returned by
    `list_submissions` with filter `missing`
  inferred: 'Given supplied from the scenario and the test setup.'
---
# Reminder to students missing a submission

## Given

Three students of the course have not submitted an assignment, and the teacher may message students and view participants in the course.

## When

a teacher confirms a message to the three students returned by `list_submissions` with filter `missing`

## Then

each of the three receives a separate message from the teacher, and the result reports three delivered
