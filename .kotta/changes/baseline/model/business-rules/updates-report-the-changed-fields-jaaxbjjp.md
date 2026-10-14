---
id: BR-01m4v5z9c3gqb7gp2fjaaxbjjp
form: business-rule
title: Updates report the changed fields
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Upsert overwrites a teacher's manual edits in Moodle)"
    - "openspec/specs/content-authoring/spec.md · Purpose"
    - "local/nitro/classes/external/save_page.php · execute"
    - "local/nitro/classes/external/save_assignment.php · execute"
    - "local/nitro/classes/local/modules.php · changed"
    - "local/nitro/tests/external/save_assignment_test.php · test_description_only_update, test_remove_due_date"
  inferred: "The promise is stated in design.md's risk list; the field lists read from save_page::execute and save_assignment::execute."
---
# Updates report the changed fields

## Rule

When `save_page` or `save_assignment` updates an existing activity, the result SHALL list the fields whose values changed, and the list SHALL be empty when the call changed nothing.

## Rationale

An upsert can overwrite edits the teacher made in Moodle; the design's answer is that the result says `updated` and lists the changed fields, and `dry_run` shows them first, so nothing changes unnoticed.

## Scope

Applies to updates by `save_page` (name, content, visibility, section) and `save_assignment` (name, description, visibility, grade, due and cut-off dates, submission types); a creation returns an empty list.
