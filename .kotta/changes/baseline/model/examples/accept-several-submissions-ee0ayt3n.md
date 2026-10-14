---
id: EX-01m4v5j7btvzrtbkb7ee0ayt3n
form: example
title: Accept several submissions
subjects:
  - BR-01m4v5j7bt5phg37604exr0zrx
  - UC-01m4v5sym7ws6zscx731a5ksp5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grade submissions /
      Scenario: Accept several submissions
    - 'local/nitro/tests/external/grade_submission_test.php · test_accept_several_submissions_on_a_scale'
  quote: >-
    a teacher confirms grade "Complete" for five students on a scale-graded
    assignment
  inferred: 'Given supplied from the test setup (two-item scale, five students).'
---
# Accept several submissions

## Given

An assignment graded on the scale "Incomplete, Complete" has five enrolled students, and the teacher may grade it and holds nitro's access gate.

## When

a teacher confirms grade "Complete" for five students on a scale-graded assignment

## Then

the five grades are saved, appear in the gradebook, and the result lists each student with the saved grade
