---
id: G-01m4v5j7bta2nyknr11dkc0da5
form: goal
title: Purpose of access-control
measured_by:
  - EX-01m4v5j7btnzdk1dmahh89ypbz
  - EX-01m4v5j7btz8xv1gqe8wsvn5wg
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/access-control/spec.md · Purpose
  quote: >-
    Guarantees that exactly as much is possible through the AI as through the
    Moodle web UI, no more and no less, and lets admins limit and switch off AI
    access
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of access-control

## Outcome

Guarantees that exactly as much is possible through the AI as through the Moodle web UI, no more and no less, and lets admins limit and switch off AI access with the tools they already use.

## Context

Faculty administrators answer to their institution for what an AI may do in Moodle, and teachers worry about what is sent in their name.

## Baseline and target

Baseline: web service tokens are issued with a narrow function list and copied by hand. Target: exactly what the web UI allows, a per-tool switch, a kill switch, and a consent screen that says what leaves Moodle.
