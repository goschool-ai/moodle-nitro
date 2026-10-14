---
id: EX-01m4v5j7btazr01wvkwvecz15c
form: example
title: Student blocks messages
subjects:
  - BR-01m4v5j7btr5a1bdhf94d7wjwn
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Respect messaging
      preferences / Scenario: Student blocks messages
    - 'local/nitro/tests/external/messaging_test.php · test_student_does_not_accept_messages'
  quote: one recipient does not accept messages from the teacher
  inferred: 'Given supplied from the test setup.'
---
# Student blocks messages

## Given

Several students are recipients. One accepts messages only from contacts, the teacher cannot message any user, and another student's account is suspended.

## When

one recipient does not accept messages from the teacher

## Then

the others receive the message and the result lists that recipient as not delivered with the reason
