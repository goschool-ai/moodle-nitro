---
id: EX-01m4v5j7btbg63g7pge1t16smd
form: example
title: Submission content only when asked
subjects:
  - BR-01m4v5j7bteex1ptp70awxzkmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Data minimisation /
      Scenario: Submission content only when asked
    - "local/nitro/tests/external/list_submissions_test.php · test_all_students_with_state"
  quote: a teacher calls `list_submissions` without `include_content`
  inferred: "Given: read from the submissions test."
---
# Submission content only when asked

## Given

Students have submitted online text with links to an assignment that the teacher may grade.

## When

a teacher calls `list_submissions` without `include_content`

## Then

the result reports submission status, timing and grading state only, and no submitted text, link or file name leaves Moodle
