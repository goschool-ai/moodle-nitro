---
id: BR-01m4v5j7bty6egr6p1g9n5mqep
form: business-rule
title: List the teacher's courses
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List the teacher's
      courses
    - "openspec/specs/course-read/spec.md · Scenario: Which Moodle this is"
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.1, 11.9"
    - "local/nitro/classes/external/list_courses.php · execute"
    - "local/nitro/tests/external/list_courses_test.php · test_teacher_courses_not_student_courses, test_no_gate_no_courses"
  quote: >-
    `list_courses` SHALL return the courses in which the user holds
    `local/nitro:use` and can manage or grade content, including courses hidden
    from students, with course ID, short name, full name, visibility
  inferred: "No source says why hidden courses are included; the rationale only restates that they are."
---
# List the teacher's courses

## Rule

`list_courses` SHALL return the courses in which the user holds `local/nitro:use` and can manage or grade content, including courses hidden from students, with course ID, short name, full name, visibility and start and end dates.

## Rationale

Every conversation starts here: the teacher names a course by its short or full name, and the AI finds its ID with this tool. Only courses the teacher works in (can manage or grade) are listed, hidden ones included, and the result names the site because an AI connected to more than one Moodle must say which one the courses come from.

## Scope

Applies to `list_courses`, which takes no parameters and returns the site's name and address with the courses. Courses where the user is only a student, or holds no `local/nitro:use`, are not listed.
