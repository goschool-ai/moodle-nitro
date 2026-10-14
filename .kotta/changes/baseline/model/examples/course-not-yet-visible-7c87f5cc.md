---
id: EX-01m4v5j7btbj8cfkgj7c87f5cc
form: example
title: Course not yet visible
subjects:
  - BR-01m4v5j7bty6egr6p1g9n5mqep
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List the teacher's
      courses / Scenario: Course not yet visible
    - "local/nitro/tests/external/list_courses_test.php · test_teacher_courses_not_student_courses"
  quote: course A is hidden from students
  inferred: "Given taken from the setup of list_courses_test.php::test_teacher_courses_not_student_courses."
---
# Course not yet visible

## Given

A user holding `local/nitro:use` is an editing teacher in course A.

## When

course A is hidden from students

## Then

it is returned and marked as hidden
