---
id: G-01m4v5j7bt6gscpre5njg4hmw3
form: goal
title: Purpose of oauth-server
measured_by:
  - EX-01m4v5j7bt61m8xvj8n412qy9v
  - EX-01m4v5j7bt0yk4y8732s8d91xz
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/oauth-server/spec.md · Purpose
  quote: >-
    Lets a teacher connect an AI assistant to Moodle by pasting one URL and
    logging in once through the normal Moodle login (and faculty SSO), without
    copying long-lived tokens.
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of oauth-server

## Outcome

Lets a teacher connect an AI assistant to Moodle by pasting one URL and logging in once through the normal Moodle login (and faculty SSO), without copying long-lived tokens. OAuth is mandatory because Copilot Cowork connectors do not support API keys.

## Context

Copilot Cowork accepts only OAuth and Moodle has no OAuth authorization server; a teacher must be able to connect alone, the day after a demo.

## Baseline and target

Baseline: hand-copied web service tokens. Target: paste one URL, log in through the normal Moodle login, approve, and revoke at any time.
