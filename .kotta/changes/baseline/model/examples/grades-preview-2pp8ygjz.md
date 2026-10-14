---
id: EX-01m4v5j7btx867560b2pp8ygjz
form: example
title: Grades preview
subjects:
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students / Scenario: Grades preview
    - "local/nitro/tests/external/grade_submission_test.php · test_accept_several_submissions_on_a_scale"
  quote: >-
    a teacher calls `grade_submission` for several students without a
    confirmation token
  inferred: "Given: read from the grading test."
---
# Grades preview

## Given

A teacher holds `local/nitro:use` and `mod/assign:grade` in a graded assignment with several students.

## When

a teacher calls `grade_submission` for several students without a confirmation token

## Then

no grade is saved, and the result lists each student with the current and the new grade and any feedback text, and a confirmation token
