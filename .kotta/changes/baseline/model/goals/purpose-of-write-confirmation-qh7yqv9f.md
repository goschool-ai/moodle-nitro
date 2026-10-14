---
id: G-01m4v5j7btnqwnk44eqh7yqv9f
form: goal
title: Purpose of write-confirmation
measured_by:
  - EX-01m4v5j7btd4c0fedkf83jmky6
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: write-confirmation
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/write-confirmation/spec.md · Purpose
  quote: >-
    Makes human approval enforceable on the server for everything that reaches
    students (messages, announcements, grades), so safety does not depend on the
    AI client following instructions: the teacher always sees
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of write-confirmation

## Outcome

Makes human approval enforceable on the server for everything that reaches students (messages, announcements, grades), so safety does not depend on the AI client following instructions: the teacher always sees the text before it is sent.

## Context

The teacher's hard rule, "show it first, send only after an explicit go", came from a message sent without approval.

## Baseline and target

Baseline: an AI client may send as soon as it decides to. Target: the server refuses to send a message, announcement or grade without a confirmed preview.
