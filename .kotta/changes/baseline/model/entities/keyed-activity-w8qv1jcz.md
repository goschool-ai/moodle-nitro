---
id: E-01m4v5t2g26gbaqfc3w8qv1jcz
form: entity
title: "Keyed activity"
used_by:
  - UC-01m4v5sy75gyt5ne3m1n3xk85j
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
  quote: "save_page and save_assignment look up the activity by key in the course module's idnumber field"
  inferred: null
---
# Keyed activity

## Meaning

A page or assignment that Nitro created or updates by a stable key the teacher chose.

## Identity

The course and the key, stored in the activity's ID number field.

## Attributes

- type (page or assignment), name, section, visibility, content (markdown in, HTML stored)
- assignment settings: submission types, grading, dates

## Invariants

- One key names at most one activity in a course.
- An update changes only the fields given in the call.
