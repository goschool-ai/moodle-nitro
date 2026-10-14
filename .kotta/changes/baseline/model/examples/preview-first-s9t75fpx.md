---
id: EX-01m4v5j7bteefmqajbs9t75fpx
form: example
title: Preview first
subjects:
  - BR-01m4v5j7btgddbv0vya6ypf3hc
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: The teacher sees the
      feedback before it is sent / Scenario: Preview first
    - 'local/nitro/tests/external/send_feedback_test.php · test_preview_then_send'
  quote: the AI calls `send_feedback` without a confirmation token
  inferred: 'Given supplied from the test setup.'
---
# Preview first

## Given

Feedback is switched on with a valid address, and the teacher holds nitro's access gate.

## When

the AI calls `send_feedback` without a confirmation token

## Then

nothing is sent and the result contains the message that would be sent, the recipient address and a confirmation token
