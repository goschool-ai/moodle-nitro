---
id: EX-01m4v5j7btwzx4mbc4a18tbhy3
form: example
title: Final deadline in the overview
subjects:
  - BR-01m4v5j7btxk1g1s7c0m7znbmm
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Course overview /
      Scenario: Final deadline in the overview
    - "local/nitro/tests/external/course_overview_test.php · test_overview_of_a_course"
  quote: an assignment has a cut-off date after its due date
  inferred: "Given taken from the setup of course_overview_test.php::test_overview_of_a_course."
---
# Final deadline in the overview

## Given

A teacher holding `local/nitro:use` in a course with an assignment due 2026-10-12 23:59 whose cut-off date is two days later.

## When

an assignment has a cut-off date after its due date

## Then

the overview lists the cut-off date next to the due date, so whether students can still submit is visible without reading the submissions
