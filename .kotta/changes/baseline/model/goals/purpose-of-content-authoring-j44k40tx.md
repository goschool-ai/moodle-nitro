---
id: G-01m4v5j7btjrya2kcyj44k40tx
form: goal
title: Purpose of content-authoring
measured_by:
  - EX-01m4v5j7bt85wz40n7afynnz6x
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/content-authoring/spec.md · Purpose
  quote: >-
    Lets the AI keep course material in Moodle in sync with the teacher's own
    source (for example, markdown in a repository): pages and assignments are
    created or updated by a
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of content-authoring

## Outcome

Lets the AI keep course material in Moodle in sync with the teacher's own source (for example, markdown in a repository): pages and assignments are created or updated by a stable key, with the same validation as the web UI, and nothing is changed or dropped silently.

## Context

The model workflow republishes pages and assignments from markdown in the teacher's repository; core web services cannot create either.

## Baseline and target

Baseline: no core web service function creates a page or an assignment. Target: create or update by a stable key, idempotently, with no silent defaults.
