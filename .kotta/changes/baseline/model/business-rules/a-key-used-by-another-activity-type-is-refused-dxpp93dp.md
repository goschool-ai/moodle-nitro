---
id: BR-01m4v5yvytezfyb4zmdxpp93dp
form: business-rule
title: A key used by another activity type is refused
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "local/nitro/classes/external/save_page.php · execute"
    - "local/nitro/classes/external/save_assignment.php · execute"
    - "local/nitro/classes/external/create_quiz.php · execute"
    - "local/nitro/tests/external/save_page_test.php · test_key_of_another_type"
  inferred: "The obligation is read from the code and its test; no spec or design text states it."
---
# A key used by another activity type is refused

## Rule

`save_page` and `save_assignment` MUST refuse a key that already belongs to an activity of another type in the course, change nothing, and name that activity's type in the error. `create_quiz` MUST refuse a key that is already used in the course.

## Rationale

Create-or-update by key must never turn one kind of activity into another or overwrite an activity the teacher did not mean. The error says what to change, as every nitro error should, so the AI can pick another key.

## Scope

Applies to `save_page`, `save_assignment` and `create_quiz` when the given key matches the ID number of an existing activity in the same course.
