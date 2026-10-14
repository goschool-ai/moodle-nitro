---
id: BR-01m4v5xg7nebbdqchs9tkqk37h
form: business-rule
title: A message confirmation covers exactly the previewed recipients
capability: messaging
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (a message_students confirmation covers exactly the recipients shown in the preview)'
    - 'local/nitro/classes/external/message_students.php · execute() ($pinned)'
    - 'local/nitro/tests/security_review_test.php · test_message_confirmation_pins_recipients'
  inferred: 'The rule text is phrased from the design''s security review line and the code; its wording is mine.'
---
# A message confirmation covers exactly the previewed recipients

## Rule

A `message_students` confirmation SHALL cover exactly the recipients listed in the preview the teacher approved. If the set of recipients the same arguments select has changed since the preview (for example, a student enrolled in the meantime), the confirmed call MUST send nothing and SHALL ask for a new preview.

## Rationale

When a message goes to a group or to all students, the arguments alone do not fix who receives it. The independent security review required that a confirmation covers exactly the recipients shown, so the teacher's approval is for those people and nobody else.

## Scope

`message_students` called with `groupid` or `all_students`, and with user IDs too. Other confirmed tools are bound by their arguments through the general confirmation protocol.
