---
id: BR-01m4v5xgmt4wajczbhkzvatfxf
form: business-rule
title: A grading call saves every grade or none
capability: grading
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/classes/external/grade_submission.php · execute() (validate everything first)'
    - 'local/nitro/tests/external/grade_submission_test.php · test_invalid_points'
    - 'local/nitro/tests/external/grade_submission_test.php · test_student_of_another_course'
    - 'openspec/specs/grading/spec.md · Scenario: Grade outside the scale'
  inferred: 'Generalised from the code comment "nothing is saved unless every entry is valid" and the tests.'
---
# A grading call saves every grade or none

## Rule

`grade_submission` SHALL check every entry of a call before it saves or previews anything. If any entry is invalid (a grade outside the maximum or the scale, a user who is not a student of the assignment, or a feedback comment on an assignment with feedback comments switched off), the whole call MUST fail with nothing saved and no confirmation token issued, and the error SHALL say what to change.

## Rationale

A batch that is saved halfway leaves the teacher unsure which students got their grade. Failing the whole call with an error that names the problem lets the AI correct it and preview again.

## Scope

Every `grade_submission` call, with or without a confirmation token. It does not cover failures inside Moodle's own save after a valid confirmation.
