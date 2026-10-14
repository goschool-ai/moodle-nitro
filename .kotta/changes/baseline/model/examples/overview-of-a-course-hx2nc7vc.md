---
id: EX-01m4v5j7bt7zqxcvsvhx2nc7vc
form: example
title: Overview of a course
subjects:
  - BR-01m4v5j7btxk1g1s7c0m7znbmm
  - UC-01m4v5sxekdm6842h7fkdsmx6a
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Course overview /
      Scenario: Overview of a course
    - "local/nitro/tests/external/course_overview_test.php · test_overview_of_a_course"
  quote: a teacher calls `course_overview` for their course
  inferred: "Given taken from the setup of course_overview_test.php::test_overview_of_a_course."
---
# Overview of a course

## Given

A teacher holding `local/nitro:use` in a course with numbered sections, activities with keys and due dates, and enrolled students.

## When

a teacher calls `course_overview` for their course

## Then

the result lists every section with its activities and their due dates, and the student count
