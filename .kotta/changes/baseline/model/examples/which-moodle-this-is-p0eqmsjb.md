---
id: EX-01m4v5j7bt6tv0c3ksp0eqmsjb
form: example
title: Which Moodle this is
subjects:
  - BR-01m4v5j7bty6egr6p1g9n5mqep
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List the teacher's
      courses / Scenario: Which Moodle this is
    - "local/nitro/tests/external/list_courses_test.php · test_teacher_courses_not_student_courses"
  quote: '`list_courses` returns'
  inferred: "Given taken from the setup of list_courses_test.php::test_teacher_courses_not_student_courses."
---
# Which Moodle this is

## Given

A teacher holding `local/nitro:use` is connected to a Moodle site that has a full name and an address.

## When

`list_courses` returns

## Then

the result names the site and its address, so an AI connected to more than one Moodle can say which one the courses come from
