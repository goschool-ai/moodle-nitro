---
id: BR-01m4v5j7btrzvtkw3jwg17zawg
form: business-rule
title: Save assignment without silent defaults
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Save assignment
      without silent defaults
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
    - "PRODUCT.md · Product Principles"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 7.4"
    - "local/nitro/classes/external/save_assignment.php · execute, defaults, effective, scale_id"
    - "local/nitro/tests/external/save_assignment_test.php · test_missing_submission_type, test_soft_and_hard_deadline, test_scale_grading"
  quote: >-
    `save_assignment` SHALL create or update an Assignment activity with a name,
    markdown description, section, visibility, submission types (online text,
    file upload), grading (maximum points or a scale), due date and
  inferred: "Value ranges, the scale lookup and the feedback-comments default read from save_assignment::execute and save_assignment::defaults."
---
# Save assignment without silent defaults

## Rule

`save_assignment` SHALL create or update an Assignment activity with a name, markdown description, section, visibility, submission types (online text, file upload), grading (maximum points or a scale), due date and cut-off date. When creating, submission types and grading MUST be given explicitly. The result SHALL echo every effective setting, including the ones taken from site defaults. It MUST require `moodle/course:manageactivities`, and `mod/assign:addinstance` when creating.

## Rationale

A Canvas assignment created from chat once came out with no submission type and zero points, so on creation nitro sets nothing silently: submission types and grading must be named, and the result echoes every effective setting, including those taken from site defaults, for the teacher to check (D10). The capability checks are those of the web UI.

## Scope

Applies to `save_assignment`: submission types are online text and file upload; grading is points (more than 0, at most 10,000) or a course or site scale named as in Moodle, never both. A new assignment keeps feedback comments on so `grade_submission` can add feedback; extensions and other submission plugins are not covered.
