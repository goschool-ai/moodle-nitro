---
id: EX-01m4v5j7btqvtgnskccbj92rd0
form: example
title: Marking workflow in use
subjects:
  - BR-01m4v5j7btsczj9z8dvzv0rtg9
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grading follows the
      assignment's settings / Scenario: Marking workflow in use
    - 'local/nitro/tests/external/grade_submission_test.php · test_marking_workflow'
  quote: the assignment uses a marking workflow and the teacher grades a submission
  inferred: 'Given supplied from the test setup (marking workflow on, no state given, so the default applies).'
---
# Marking workflow in use

## Given

An assignment graded out of 10 points uses marking workflow, and the teacher does not name a workflow state.

## When

the assignment uses a marking workflow and the teacher grades a submission

## Then

the grade is saved in the workflow state the teacher chose (default: not released), and the preview says students will not see it until released
