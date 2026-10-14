---
id: EX-01m4v5xjjcpcz09a9jyyckwz12
form: example
title: Regrade without a new comment
capability: grading
subjects:
  - BR-01m4v5xgwnjp2c6zd5dkkvxm16
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/tests/external/grade_submission_test.php · test_half_points_with_feedback'
  inferred: 'Read from the test.'
---
# Regrade without a new comment

## Given

A student has 5 points with the feedback comment "Késve jött, ezért fél pont."

## When

the teacher confirms a new grade of 6 points for that student without feedback

## Then

the grade becomes 6 and the comment still reads "fél pont"
