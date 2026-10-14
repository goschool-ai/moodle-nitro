---
id: EX-01m4v5j7btnzdk1dmahh89ypbz
form: example
title: Gate does not add power
subjects:
  - BR-01m4v5j7btg9g46903yer3cvj5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Access gate
      capability / Scenario: Gate does not add power
    - "local/nitro/tests/external/save_page_test.php · test_requires_manage_activities"
    - "local/nitro/tests/external/quiz_tools_test.php · test_missing_permission"
  quote: >-
    a user holds `local/nitro:use` but lacks `moodle/course:manageactivities` in
    a course
  inferred: "Given: read from the save_page and quiz tests."
---
# Gate does not add power

## Given

nitro is switched on, and the user is enrolled in the course with a role, such as non-editing teacher, that cannot add or edit activities in the web UI.

## When

a user holds `local/nitro:use` but lacks `moodle/course:manageactivities` in a course

## Then

content-creating tools fail in that course exactly as the web UI would forbid it
