---
id: E-01m4v5t2a4rv1eh60kn12p4s37
form: entity
title: "Tool call record"
used_by:
  - UC-01m4v5t00xh3nwy7f83gkbmrr8
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D8. Audit event"
  quote: "other stores only tool name, client ID, outcome and affected object IDs."
  inferred: null
---
# Tool call record

## Meaning

The record in Moodle's event log that one tool was called.

## Identity

The Moodle log entry of the event `\local_nitro\event\tool_called`.

## Attributes

- user, course, time
- tool name, client ID, outcome, affected object IDs

## Invariants

- It carries no message, page or submission content.
- Every call, successful or denied, has one.
