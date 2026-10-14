---
id: EX-01m4v5j7btrtt2p8dgkanvkahw
form: example
title: Late submission
subjects:
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions / Scenario: Late submission
    - "local/nitro/tests/external/list_submissions_test.php · test_all_students_with_state"
  quote: a student submitted after the due date without an extension
  inferred: "Given taken from the setup of list_submissions_test.php::test_all_students_with_state."
---
# Late submission

## Given

An assignment with a due date; a teacher holding `local/nitro:use` and `mod/assign:grade` lists its submissions.

## When

a student submitted after the due date without an extension

## Then

their entry is marked late
