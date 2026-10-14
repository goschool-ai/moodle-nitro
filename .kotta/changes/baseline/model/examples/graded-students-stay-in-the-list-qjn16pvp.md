---
id: EX-01m4v5j7btyz4575e7qjn16pvp
form: example
title: Graded students stay in the list
subjects:
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions / Scenario: Graded students stay in the list
    - "local/nitro/tests/external/list_submissions_test.php · test_all_students_with_state"
  quote: a submission has been graded
  inferred: "Given taken from the setup of list_submissions_test.php::test_all_students_with_state."
---
# Graded students stay in the list

## Given

An assignment worth 10 points whose due date has passed; a teacher holding `local/nitro:use` and `mod/assign:grade` lists its submissions.

## When

a submission has been graded

## Then

it is still returned as submitted, marked as graded with its grade, so a status check never loses accepted students
