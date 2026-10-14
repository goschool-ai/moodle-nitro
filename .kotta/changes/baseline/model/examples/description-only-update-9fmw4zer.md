---
id: EX-01m4v5j7btae8mmy749fmw4zer
form: example
title: Description-only update
subjects:
  - BR-01m4v5j7btd3c5zvqjan6epr7s
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Partial updates /
      Scenario: Description-only update
    - "local/nitro/tests/external/save_assignment_test.php · test_description_only_update"
  quote: a teacher updates an existing assignment's description only
  inferred: "Given taken from the setup of save_assignment_test.php::test_description_only_update."
---
# Description-only update

## Given

An assignment created through `save_assignment` with a key, a name, section 1, hidden, online text and file submission, 20 points, and due and cut-off dates; its maximum number of files was later changed in the web UI.

## When

a teacher updates an existing assignment's description only

## Then

its name, due date, cut-off date, visibility and grading settings are unchanged
