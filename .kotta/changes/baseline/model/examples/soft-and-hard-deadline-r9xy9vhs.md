---
id: EX-01m4v5j7bt1f117rter9xy9vhs
form: example
title: Soft and hard deadline
subjects:
  - BR-01m4v5j7btrzvtkw3jwg17zawg
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Save assignment
      without silent defaults / Scenario: Soft and hard deadline
    - "local/nitro/tests/external/save_assignment_test.php · test_soft_and_hard_deadline"
  quote: >-
    a teacher creates an assignment with due date Friday 23:59 and cut-off date
    Sunday 20:00
  inferred: "Given taken from the setup of save_assignment_test.php::test_soft_and_hard_deadline."
---
# Soft and hard deadline

## Given

A teacher who holds `local/nitro:use` and may manage activities, with the time zone Europe/Budapest; no assignment with the given key exists yet.

## When

a teacher creates an assignment with due date Friday 23:59 and cut-off date Sunday 20:00

## Then

students can submit late until Sunday 20:00, and the result shows both dates
