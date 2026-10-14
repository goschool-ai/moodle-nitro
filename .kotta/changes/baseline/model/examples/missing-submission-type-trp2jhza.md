---
id: EX-01m4v5j7btyq08d1fxtrp2jhza
form: example
title: Missing submission type
subjects:
  - BR-01m4v5j7btrzvtkw3jwg17zawg
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Save assignment
      without silent defaults / Scenario: Missing submission type
    - "local/nitro/tests/external/save_assignment_test.php · test_missing_submission_type"
  quote: a teacher creates an assignment without submission types
  inferred: "Given taken from the setup of save_assignment_test.php::test_missing_submission_type."
---
# Missing submission type

## Given

A teacher who holds `local/nitro:use` and may manage activities; no assignment with the given key exists in the course.

## When

a teacher creates an assignment without submission types

## Then

nothing is created and the error says submission types must be given
