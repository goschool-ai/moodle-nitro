---
id: EX-01m4v5j7btyazqa2pe7w3yexht
form: example
title: The teacher sees whose grade it is
subjects:
  - BR-01m4v5j7bt5phg37604exr0zrx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grade submissions /
      Scenario: The teacher sees whose grade it is
    - 'local/nitro/tests/external/grade_submission_test.php · test_accept_several_submissions_on_a_scale'
  quote: '`grade_submission` returns a preview'
  inferred: 'Given supplied from the test, which checks names in the preview.'
---
# The teacher sees whose grade it is

## Given

An assignment has enrolled students, and the AI names the students by user ID in a `grade_submission` call.

## When

`grade_submission` returns a preview

## Then

every entry carries the student's name, not only their user ID
