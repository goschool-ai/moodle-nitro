---
id: EX-01m4v5j7btjd1zg7q80kf5tv29
form: example
title: Teacher rewrites it
subjects:
  - BR-01m4v5j7btgddbv0vya6ypf3hc
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: The teacher sees the
      feedback before it is sent / Scenario: Teacher rewrites it
    - 'local/nitro/tests/external/send_feedback_test.php · test_changed_text_needs_a_new_preview'
  quote: the teacher changes the text and the AI calls again with the old token
  inferred: 'Given supplied from the test.'
---
# Teacher rewrites it

## Given

The AI has called `send_feedback` and received a preview and a confirmation token for one text.

## When

the teacher changes the text and the AI calls again with the old token

## Then

nothing is sent and the result asks for a new preview
