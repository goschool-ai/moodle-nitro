---
id: BR-01m4v5j7bt6b8710qcmzqc20pj
form: business-rule
title: Message students individually
capability: messaging
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Message students
      individually
    - 'openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why (individual reminders in the weekly loop)'
    - 'PRODUCT.md · Operating Context; Product Principles 1–2'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (separate groups mode)'
    - 'local/nitro/classes/external/message_students.php · execute(), recipients()'
  quote: >-
    `message_students` SHALL send a message written in markdown to a set of
    students of one course, selected by user IDs, by group, or as all students.
---
# Message students individually

## Rule

`message_students` SHALL send a message written in markdown to a set of students of one course, selected by user IDs, by group, or as all students. Each recipient SHALL receive it as a separate one-to-one message from the teacher; recipients MUST NOT see each other. It MUST require `moodle/site:sendmessage` and `moodle/course:viewparticipants` in the course, and is subject to the confirmation protocol of `write-confirmation`.

## Rationale

Reminders to individual students who are behind are part of the teacher's real weekly loop that the tool set follows (proposal, Why; PRODUCT.md, Operating Context), so each reminder is a personal message from the teacher. The tool needs the same capabilities as sending a message in the web UI (principle 1). Because the message reaches students, it waits for the teacher's explicit approval (principle 2, D5).

## Scope

The `message_students` tool, within one course. Recipients can only be active students of that course and, in separate groups mode, only members of the teacher's own groups. It does not cover announcements (`post_announcement`), forum replies or reading the inbox (deferred to `nitro-pilot`).
