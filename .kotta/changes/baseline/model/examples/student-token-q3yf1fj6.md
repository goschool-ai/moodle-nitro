---
id: EX-01m4v5j7btgfyn9h0tq3yf1fj6
form: example
title: Student token
subjects:
  - BR-01m4v5j7bt8723rb1rgvkrcn29
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Same capabilities as
      the web UI / Scenario: Student token
    - "local/nitro/tests/external/list_submissions_test.php · test_student_gets_no_data"
  quote: a user enrolled only as a student calls `list_submissions` for an assignment
  inferred: "Given: read from the submissions test."
---
# Student token

## Given

The user holds `local/nitro:use` but is enrolled in the course only as a student, so they lack `mod/assign:grade`.

## When

a user enrolled only as a student calls `list_submissions` for an assignment

## Then

the call fails with a permission error and no submission data is returned
