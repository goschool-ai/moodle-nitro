---
id: BR-01m4v5j7btfttg85w3vrm5t4br
form: business-rule
title: List participants
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/course-read/spec.md · Requirement: List participants'
    - "openspec/specs/course-read/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (default reads leave out identity fields)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (separate groups mode)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.3"
    - "local/nitro/classes/external/list_participants.php · execute"
    - "local/nitro/classes/local/access.php · visible_users"
    - "local/nitro/tests/external/list_participants_test.php · test_filter_by_role, test_never_accessed"
  quote: >-
    `list_participants` SHALL return the enrolled users of a course with their
    roles, groups and last access to the course (or that they never accessed
    it), optionally filtered by role or
  inferred: "The identity-field condition (site identity settings) read from list_participants::execute."
---
# List participants

## Rule

`list_participants` SHALL return the enrolled users of a course with their roles, groups and last access to the course (or that they never accessed it), optionally filtered by role or group, following the data minimisation rules of `access-control`. It MUST require `moodle/course:viewparticipants`.

## Rationale

The teacher asks who takes the course and whether they ever showed up, and individual reminders need the students' user IDs. Email address and ID number are left out unless asked for, as part of the data minimisation that keeps identity data out of the AI's conversation by default.

## Scope

Applies to `list_participants` for one course where the user holds `local/nitro:use` and `moodle/course:viewparticipants`; roles are named by short name, and a `never_accessed` filter returns only users who never opened the course. In separate groups mode a user without `moodle/site:accessallgroups` sees only their own groups; identity fields come only with `include_identity` and only where the site shows them to the teacher.
