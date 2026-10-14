---
id: EX-01m4v5xjcgta5twrw76xgcv6dp
form: example
title: One invalid grade in a batch
capability: grading
subjects:
  - BR-01m4v5xgmt4wajczbhkzvatfxf
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/tests/external/grade_submission_test.php · test_invalid_points'
  inferred: 'Read from the test.'
---
# One invalid grade in a batch

## Given

An assignment is graded out of 10 points and has two students.

## When

the AI asks for 8 points for the first student and 11 points for the second

## Then

nothing is saved, no confirmation token is issued, and the error says to give a number from 0 to 10
