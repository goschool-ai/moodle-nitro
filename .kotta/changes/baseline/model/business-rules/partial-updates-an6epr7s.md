---
id: BR-01m4v5j7btd3c5zvqjan6epr7s
form: business-rule
title: Partial updates
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/content-authoring/spec.md · Requirement: Partial updates'
    - "openspec/specs/content-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Upsert overwrites a teacher's manual edits)"
    - "local/nitro/classes/local/modules.php · current_data, update"
    - "local/nitro/tests/external/save_assignment_test.php · test_description_only_update"
  quote: >-
    On update, only the fields given in the call SHALL change; every other
    setting (name, section, visibility, dates, grading) MUST stay as it is.
  inferred: "The example of a setting a plain update would reset read from the modules::current_data docblock."
---
# Partial updates

## Rule

On update, only the fields given in the call SHALL change; every other setting (name, section, visibility, dates, grading) MUST stay as it is.

## Rationale

Republishing from the repository must not overwrite what the teacher changed in Moodle and did not mean to change, so only the fields given are overlaid on the activity's current form data (D10). Starting from the current form data keeps even settings nitro has no parameter for, which a plain update would reset (for example switching off every submission type).

## Scope

Applies to updates by `save_page` and `save_assignment` of an activity found by its key: an omitted parameter keeps its value, including settings nitro does not manage, such as the maximum number of files. Creation, and `create_quiz`, which never updates, are not covered.
