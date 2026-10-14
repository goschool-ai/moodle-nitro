---
id: EX-01m4v5j7bt89jmqmw3sva1ctck
form: example
title: Hidden activities
subjects:
  - BR-01m4v5j7btxk1g1s7c0m7znbmm
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Course overview /
      Scenario: Hidden activities
    - "local/nitro/tests/external/course_overview_test.php · test_overview_of_a_course"
  quote: the course contains hidden activities
  inferred: "Given taken from the setup of course_overview_test.php::test_overview_of_a_course."
---
# Hidden activities

## Given

A teacher holding `local/nitro:use` who can see hidden activities, as an editing teacher can, in a course that has a quiz hidden from students.

## When

the course contains hidden activities

## Then

they are included and marked as hidden, as a teacher sees them in the web UI
