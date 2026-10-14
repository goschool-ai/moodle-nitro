---
id: UC-01m4v5sxq7mrw8dcspk5n11dd7
form: use-case
title: "Follow up with students who are behind"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btga1g4n5spb0wkxwx
  - G-01m4v5j7btnqwnk44eqh7yqv9f
refines:
  - BR-01m4v5j7bt6b8710qcmzqc20pj
  - BR-01m4v5j7btr5a1bdhf94d7wjwn
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
  - BR-01m4v5j7bt18sj6vqww7fed3kb
  - BR-01m4v5xg7nebbdqchs9tkqk37h
  - BR-01m4v5xgej11sz0kb9pgv0qvwc
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
  quote: "This mirrors the teacher's own hard rule (\"show it first, send only after an explicit go\"), which came from a message sent without approval."
  inferred: null
---
# Follow up with students who are behind

## Intent

The teacher writes to the students who are behind, one personal message each, and approves every message before Moodle sends it.

## Preconditions

The assistant is connected; the teacher can message the students in Moodle.

## Main success scenario

1. The teacher asks the assistant to write to the students who are behind.
2. The assistant finds them (Check where the course stands) and drafts one message per student.
3. The first `message_students` call returns a preview and a confirmation token; the assistant shows the preview word for word.
4. The teacher approves; the assistant repeats the call with the token.
5. Moodle sends each message through its own messaging, respecting each student's preferences.

## Alternatives

- The teacher asks for a change: the assistant makes a new preview.
- A student blocks messages or is not in the course: the preview says so and they are skipped.
- The arguments change after the preview: the token is refused.
