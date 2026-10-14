---
id: EX-01m4v5j7bt4a8jdby8h0ac7g89
form: example
title: Overdue submission
subjects:
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions / Scenario: Overdue submission
    - "local/nitro/tests/external/list_submissions_test.php · test_all_students_with_state"
  quote: >-
    a student has submitted nothing and the deadline (with any extension) has
    passed
  inferred: "Given taken from the setup of list_submissions_test.php::test_all_students_with_state."
---
# Overdue submission

## Given

An assignment with a due date; a teacher holding `local/nitro:use` and `mod/assign:grade` lists its submissions.

## When

a student has submitted nothing and the deadline (with any extension) has passed

## Then

their entry is marked overdue
