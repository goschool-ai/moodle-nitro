---
id: EX-01m4v5j7bt03dbn8672jf4ksrj
form: example
title: Recipient not in the course
subjects:
  - BR-01m4v5j7bt6b8710qcmzqc20pj
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Message students
      individually / Scenario: Recipient not in the course
    - 'local/nitro/tests/external/messaging_test.php · test_recipient_not_in_course'
  quote: a user ID in the recipient list is not enrolled in the course
  inferred: 'Given supplied from the test setup.'
---
# Recipient not in the course

## Given

A course has enrolled students, and a user exists who is not enrolled in it.

## When

a user ID in the recipient list is not enrolled in the course

## Then

nothing is sent and the error names that user ID
