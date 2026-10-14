---
id: EX-01m4v5j7bt1ef0r1y14mtr0p49
form: example
title: Cut-off before due date
subjects:
  - BR-01m4v5j7btrzvtkw3jwg17zawg
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Save assignment
      without silent defaults / Scenario: Cut-off before due date
    - "local/nitro/tests/external/save_assignment_test.php · test_cutoff_before_due"
  quote: the cut-off date is earlier than the due date
  inferred: "Given taken from the setup of save_assignment_test.php::test_cutoff_before_due."
---
# Cut-off before due date

## Given

A teacher who holds `local/nitro:use` and may manage activities saves an assignment with a due date of 2026-10-09T23:59.

## When

the cut-off date is earlier than the due date

## Then

nothing is saved and the error explains the conflict
