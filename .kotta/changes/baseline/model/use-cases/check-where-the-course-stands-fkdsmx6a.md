---
id: UC-01m4v5sxekdm6842h7fkdsmx6a
form: use-case
title: "Check where the course stands"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btppfb910xdzt6pwqc
refines:
  - BR-01m4v5j7bty6egr6p1g9n5mqep
  - BR-01m4v5j7btxk1g1s7c0m7znbmm
  - BR-01m4v5j7btfttg85w3vrm5t4br
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
  - BR-01m4v5j7bt6kkmgxn09t55tm0k
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
  - BR-01m4v5j7bteex1ptp70awxzkmt
  - BR-01m4v5zabe039r7af5sajtd3h1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes"
    - "local/nitro/classes/mcp/server.php · instructions"
  quote: "a daily \"who is behind?\" check"
  inferred: "The step order as the server instructions tell the AI to work."
---
# Check where the course stands

## Intent

The teacher finds out where a course stands: who is behind, who never showed up, what the course contains, what students wrote.

## Preconditions

The assistant is connected; the teacher can see the course in Moodle.

## Main success scenario

1. The teacher names a course; the assistant finds it among the teacher's courses.
2. The assistant reads the course overview: sections, activities, due dates, hidden items.
3. It lists participants with their last access and submissions with their grading state, filtered to missing or late where asked.
4. Where needed it reads an activity's text or the course forums.
5. It answers the teacher; nothing in the course changes.

## Alternatives

- The teacher can see only some groups: only those students are listed.
- Identity fields or submission content are returned only when the call asks for them.
