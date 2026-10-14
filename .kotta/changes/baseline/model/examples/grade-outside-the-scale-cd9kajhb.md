---
id: EX-01m4v5j7btqg82p0d7cd9kajhb
form: example
title: Grade outside the scale
subjects:
  - BR-01m4v5j7bt5phg37604exr0zrx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grade submissions /
      Scenario: Grade outside the scale
    - 'local/nitro/tests/external/grade_submission_test.php · test_invalid_points'
    - 'local/nitro/tests/external/grade_submission_test.php · test_unknown_scale_item_lists_valid_values'
  quote: a grade exceeds the maximum or names a scale item that does not exist
  inferred: 'Given supplied from the two tests (10-point assignment; scale "Incomplete, Complete").'
---
# Grade outside the scale

## Given

An assignment is graded out of 10 points or on a scale, and the teacher asks to grade one or more of its students.

## When

a grade exceeds the maximum or names a scale item that does not exist

## Then

nothing is saved and the error lists the valid values
