---
id: UC-01m4v5sy75gyt5ne3m1n3xk85j
form: use-case
title: "Publish pages and assignments"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btjrya2kcyj44k40tx
refines:
  - BR-01m4v5j7bt6hjy4k2jpt0mkzk9
  - BR-01m4v5j7bt9rfe2s8t3a1jgyv3
  - BR-01m4v5j7btd3c5zvqjan6epr7s
  - BR-01m4v5j7btg43hqqy6ypbhb7xa
  - BR-01m4v5j7btmzq4qn0n824rg0cn
  - BR-01m4v5j7btrzvtkw3jwg17zawg
  - BR-01m4v5z9ryn9rk18n8d8ccw7r3
  - BR-01m4v5yvytezfyb4zmdxpp93dp
  - BR-01m4v5z9jxx09fn5zbf026qxkr
  - BR-01m4v5z9ywbqaw6d4n303nmket
  - BR-01m4v5z9c3gqb7gp2fjaaxbjjp
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
  quote: "This makes republishing from the teacher's repository idempotent, like the Canvas scripts that matched pages by title, but with a key that survives renaming."
  inferred: null
---
# Publish pages and assignments

## Intent

The teacher publishes and republishes pages and assignments from markdown, with a stable key, so the course stays in sync with their source.

## Preconditions

The assistant is connected; the teacher can edit the course.

## Main success scenario

1. The teacher gives the assistant markdown for a page or an assignment, with a key.
2. The assistant may call `save_page` or `save_assignment` with `dry_run` first to show what would change.
3. The call creates the activity, or updates the one with that key, touching only the fields given.
4. The result reports the effective settings and anything Moodle's HTML cleaning removed.

## Alternatives

- An assignment is created without submission type or grading: the call is refused, no silent defaults.
- A date has no offset or the cut-off precedes the due date: the call is refused.
