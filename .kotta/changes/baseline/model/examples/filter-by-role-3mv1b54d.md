---
id: EX-01m4v5j7btf7se2f083mv1b54d
form: example
title: Filter by role
subjects:
  - BR-01m4v5j7btfttg85w3vrm5t4br
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List participants /
      Scenario: Filter by role
    - "local/nitro/tests/external/list_participants_test.php · test_filter_by_role"
  quote: a teacher calls `list_participants` with role `student`
  inferred: "Given taken from the setup of list_participants_test.php::test_filter_by_role."
---
# Filter by role

## Given

A course with a teacher who holds `local/nitro:use` and `moodle/course:viewparticipants`, and two enrolled students.

## When

a teacher calls `list_participants` with role `student`

## Then

only users with the student role are returned
