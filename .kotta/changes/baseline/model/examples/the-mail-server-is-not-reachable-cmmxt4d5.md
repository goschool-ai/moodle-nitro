---
id: EX-01m4v5j7btfmtn6p0zcmmxt4d5
form: example
title: The mail server is not reachable
subjects:
  - BR-01m4v5j7bt8fgb45bv3ex3br5x
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro /
      Scenario: The mail server is not reachable
    - local/nitro/tests/external/send_feedback_test.php · test_unreachable_mail_server_queues_a_retry
  quote: >-
    the mail server refuses or cannot be resolved at the moment the teacher
    approves the message
  inferred: Given taken from the setup of the PHPUnit test.
---
# The mail server is not reachable

## Given

The feedback tool is on with a feedback address configured, and the teacher has seen the preview of their report.

## When

the mail server refuses or cannot be resolved at the moment the teacher approves the message

## Then

the result says the message is queued rather than sent, and Moodle keeps retrying it, so the report is never silently lost
