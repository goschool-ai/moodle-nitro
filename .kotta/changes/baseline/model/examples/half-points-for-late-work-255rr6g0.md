---
id: EX-01m4v5j7btc59gq52n255rr6g0
form: example
title: Half points for late work
subjects:
  - BR-01m4v5j7bt5phg37604exr0zrx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grade submissions /
      Scenario: Half points for late work
    - 'local/nitro/tests/external/grade_submission_test.php · test_half_points_with_feedback'
  quote: >-
    a teacher confirms 5 out of 10 points for a late submission with a feedback
    comment
  inferred: 'Given supplied from the test setup.'
---
# Half points for late work

## Given

An assignment is graded out of 10 points with feedback comments on and no marking workflow, and a student who submitted late has no grade yet.

## When

a teacher confirms 5 out of 10 points for a late submission with a feedback comment

## Then

the grade and the comment are saved on that student's submission
