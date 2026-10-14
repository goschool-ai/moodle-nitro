---
id: EX-01m4v5j7btcdtg28z3jtdj5h7d
form: example
title: Assignment instructions
subjects:
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read an activity /
      Scenario: Assignment instructions
    - "local/nitro/tests/external/read_activity_test.php · test_assignment_description_and_dates"
  quote: the module is an assignment
  inferred: "Given taken from the setup of read_activity_test.php::test_assignment_description_and_dates."
---
# Assignment instructions

## Given

A teacher holding `local/nitro:use` in a course with an assignment that has a description and a due date.

## When

the module is an assignment

## Then

its description and due dates are returned
