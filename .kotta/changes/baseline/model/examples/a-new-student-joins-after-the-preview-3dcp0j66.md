---
id: EX-01m4v5xhybqjm03db03dcp0j66
form: example
title: A new student joins after the preview
capability: messaging
subjects:
  - BR-01m4v5xg7nebbdqchs9tkqk37h
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/tests/security_review_test.php · test_message_confirmation_pins_recipients'
  inferred: 'Read from the test.'
---
# A new student joins after the preview

## Given

The teacher previewed a message to all students of a course that has one student.

## When

a second student enrols, and the AI confirms with the token from that preview

## Then

nothing is sent and the call is refused, so a new preview is needed
