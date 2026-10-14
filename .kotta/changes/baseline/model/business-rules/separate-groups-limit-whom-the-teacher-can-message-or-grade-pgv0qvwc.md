---
id: BR-01m4v5xgej11sz0kb9pgv0qvwc
form: business-rule
title: Separate groups limit whom the teacher can message or grade
capability: messaging
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (separate groups mode, access::visible_users())'
    - 'local/nitro/classes/local/access.php · visible_users()'
    - 'local/nitro/classes/external/message_students.php · execute()'
    - 'local/nitro/classes/external/grade_submission.php · execute()'
    - 'local/nitro/tests/security_review_test.php · test_separate_groups_limit_what_a_teacher_sees'
  inferred: 'The rule is phrased from the design''s security review and the code; no test covers the messaging half.'
---
# Separate groups limit whom the teacher can message or grade

## Rule

In a course or activity in separate groups mode, a teacher without `moodle/site:accessallgroups` SHALL be able to message or grade only students of their own groups. A `message_students` or `grade_submission` call that names a student or a group outside those groups MUST change nothing and SHALL be refused with an error.

## Rationale

Through the AI exactly as much is possible as in the web UI, and the web UI restricts such a teacher to their own groups. The security review found this missing and fixed it.

## Scope

`message_students` (course group mode) and `grade_submission` (the assignment's group mode and grouping). The same restriction on the read tools (`list_participants`, `list_submissions`) belongs to course reading. It does not apply to users who hold `moodle/site:accessallgroups`.
