---
id: EX-01m4v5j7btweb9x3ybzk6y6k1c
form: example
title: Missing submissions
subjects:
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
  - UC-01m4v5sw613zsg92tcczb36t32
  - UC-01m4v5sxekdm6842h7fkdsmx6a
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions / Scenario: Missing submissions
    - "local/nitro/tests/external/list_submissions_test.php · test_filters"
  quote: a teacher calls `list_submissions` with filter `missing` after the due date
  inferred: "Given taken from the setup of list_submissions_test.php::test_filters."
---
# Missing submissions

## Given

An assignment whose due date has passed; some students submitted and one submitted nothing.

## When

a teacher calls `list_submissions` with filter `missing` after the due date

## Then

only students without a submitted submission are returned
