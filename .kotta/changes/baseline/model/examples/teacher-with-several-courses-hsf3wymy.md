---
id: EX-01m4v5j7btetdz0700hsf3wymy
form: example
title: Teacher with several courses
subjects:
  - BR-01m4v5j7bty6egr6p1g9n5mqep
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List the teacher's
      courses / Scenario: Teacher with several courses
    - "local/nitro/tests/external/list_courses_test.php · test_teacher_courses_not_student_courses"
  quote: >-
    a teacher of courses A and B, who is a student in course C, calls
    `list_courses`
  inferred: "Given taken from the setup of list_courses_test.php::test_teacher_courses_not_student_courses."
---
# Teacher with several courses

## Given

A user holds `local/nitro:use` and is an editing teacher in courses A and B and a student in course C.

## When

a teacher of courses A and B, who is a student in course C, calls `list_courses`

## Then

A and B are returned and C is not
