---
id: EX-01m4v5j7bt0gv57mr0pj4q0m1h
form: example
title: A missing tool
subjects:
  - BR-01m4v5j7bt8fgb45bv3ex3br5x
  - UC-01m4v5syt6d55ed03mqvjk6t29
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro /
      Scenario: A missing tool
    - 'local/nitro/tests/external/send_feedback_test.php · test_preview_then_send'
  quote: >-
    the AI hits a task nitro has no tool for and the teacher approves sending
    feedback about it
  inferred: 'Given supplied from the test setup (feedback on, address set, teacher with the gate).'
---
# A missing tool

## Given

Feedback is switched on, a valid feedback address is configured, and the teacher holds nitro's access gate.

## When

the AI hits a task nitro has no tool for and the teacher approves sending feedback about it

## Then

the message arrives at the configured address with the text, the site and the nitro version
