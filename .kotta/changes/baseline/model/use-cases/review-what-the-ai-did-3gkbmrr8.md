---
id: UC-01m4v5t00xh3nwy7f83gkbmrr8
form: use-case
title: "Review what the AI did"
level: user-goal
actor:
  - A-01m4v5svz9q5s0fwj4xnjp14j4
goal:
  - G-01m4v5j7bt1x5g1t9gepbgv3wv
refines:
  - BR-01m4v5j7btrxsgv3r22s4jrnnz
  - BR-01m4v5j7bt5v2zqqzj16kcknm0
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D8. Audit event"
    - "local/nitro/README.md · 7. Administration"
  quote: "every tool call is logged as the event AI tool called with user, course and tool, but without the content"
  inferred: null
---
# Review what the AI did

## Intent

The administrator sees what AI tools did on the site, without the content of messages or pages.

## Preconditions

Nitro has been used on the site.

## Main success scenario

1. The administrator opens the standard Moodle logs and filters for the event AI tool called.
2. Each entry shows user, course, tool, client and outcome, with affected object IDs, but no message or content bodies.

## Alternatives

- A denied call is logged too, with its outcome.
