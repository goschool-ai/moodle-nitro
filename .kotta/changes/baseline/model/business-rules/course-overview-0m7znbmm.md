---
id: BR-01m4v5j7btxk1g1s7c0m7znbmm
form: business-rule
title: Course overview
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/course-read/spec.md · Requirement: Course overview'
    - "openspec/specs/course-read/spec.md · Purpose"
    - "openspec/specs/course-read/spec.md · Scenario: Final deadline in the overview"
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.2, 11.10"
    - "local/nitro/classes/external/course_overview.php · execute, dates"
    - "local/nitro/tests/external/course_overview_test.php · test_overview_of_a_course"
  quote: >-
    `course_overview` SHALL return, for a course, its sections in order with
    their activities (ID, key, type, name, visibility, due date where
    applicable) and the number of enrolled students.
  inferred: "Who counts as a student (active enrolment with mod/assign:submit) and when hidden items are shown read from course_overview::execute."
---
# Course overview

## Rule

`course_overview` SHALL return, for a course, its sections in order with their activities (ID, key, type, name, visibility, due date where applicable) and the number of enrolled students.

## Rationale

It answers the daily question "where does my course stand?" and gives the AI the activity IDs, keys and due dates it must see before changing anything, as the server instructions tell it. Hidden activities are listed as a teacher sees them in the web UI, and an assignment's cut-off date is added next to its due date because whether students can still submit depends on it, which Moodle's own activity dates leave out.

## Scope

Applies to `course_overview` for one course where the user holds `local/nitro:use`. It lists the sections and activities the user may see (hidden ones when they can view hidden sections and activities), every date Moodle shows for an activity, and counts actively enrolled users who can submit assignments as students. It does not return activity text (`read_activity`), forum posts (`list_forum_posts`) or submissions (`list_submissions`).
