---
id: EX-01m4v5j7bt48rrpg1am18kkqjh
form: example
title: Date without offset
subjects:
  - BR-01m4v5j7btmzq4qn0n824rg0cn
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Dates are
      unambiguous / Scenario: Date without offset
    - "local/nitro/tests/external/save_assignment_test.php · test_soft_and_hard_deadline"
  quote: 'a due date is given as `2026-10-09T23:59`'
  inferred: "Given taken from the setup of save_assignment_test.php::test_soft_and_hard_deadline."
---
# Date without offset

## Given

The teacher's Moodle time zone is Europe/Budapest (UTC+02:00 on that date) and their language is English.

## When

a due date is given as `2026-10-09T23:59`

## Then

it is stored as 23:59 in the user's Moodle time zone, and the result shows `2026-10-09T23:59:00+02:00` with the weekday Friday
