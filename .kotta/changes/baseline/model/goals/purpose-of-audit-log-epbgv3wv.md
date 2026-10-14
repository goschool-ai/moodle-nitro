---
id: G-01m4v5j7bt1x5g1t9gepbgv3wv
form: goal
title: Purpose of audit-log
measured_by:
  - EX-01m4v5j7btnpgy3w4ahpff0hnx
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: audit-log
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/audit-log/spec.md · Purpose
  quote: >-
    Makes everything the AI did in Moodle visible afterwards, in the log reports
    admins and teachers already use.
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of audit-log

## Outcome

Makes everything the AI did in Moodle visible afterwards, in the log reports admins and teachers already use.

## Context

An institution must be able to see afterwards what AI tools did in its Moodle, without the log becoming a second copy of student data.

## Baseline and target

Baseline: an AI acting through Moodle leaves no trace of its own. Target: one log event per tool call, with no content.
