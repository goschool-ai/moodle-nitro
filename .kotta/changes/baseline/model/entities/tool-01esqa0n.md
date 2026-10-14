---
id: E-01m4v5t23zv8d8bp9z01esqa0n
form: entity
title: "Tool"
used_by:
  - UC-01m4v5t0c3b5wt6eggggfwqy05
  - UC-01m4v5szjk7rmysaj066c8mz69
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D1. Architecture"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path"
  quote: "The MCP layer is a thin adapter: it maps tools/list to the allowlisted functions"
  inferred: null
---
# Tool

## Meaning

One capability offered to AI clients over MCP, backed by a Moodle external function of the same name.

## Identity

Its short name, for example `list_submissions` or `message_students`.

## Attributes

- description and input schema generated from the external function
- annotations: read-only or destructive
- whether it needs confirmation
- whether the admin allows it

## Invariants

- Only allowed tools are listed or callable.
- A new tool is added to the allowed list on upgrade.
