---
id: EX-01m4v5j7btqfnjhfdvzyjznw85
form: example
title: Feedback carries no course data
subjects:
  - BR-01m4v5j7bt63kng1skzff30aq7
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: No third-party data
      flow / Scenario: Feedback carries no course data
    - "openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro"
    - "local/nitro/tests/external/send_feedback_test.php · test_preview_then_send"
  quote: a teacher approves sending feedback about nitro
  inferred: "Given: read from the feedback spec and test."
---
# Feedback carries no course data

## Given

The feedback tool is switched on, a feedback address is set, and the teacher has seen the preview of the exact message.

## When

a teacher approves sending feedback about nitro

## Then

the message contains only their text, the site name and URL, and the nitro and Moodle versions
