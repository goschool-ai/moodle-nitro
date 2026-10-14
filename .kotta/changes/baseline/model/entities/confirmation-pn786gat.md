---
id: E-01m4v5t1x7er5z3tw4pn786gat
form: entity
title: "Confirmation"
used_by:
  - UC-01m4v5sxq7mrw8dcspk5n11dd7
  - UC-01m4v5sxzyxjm0ekr72gwwwwq1
  - UC-01m4v5sym7ws6zscx731a5ksp5
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/db/install.xml · local_nitro_confirm"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens"
  quote: "Confirmations are stored in local_nitro_confirm (user, tool, SHA-256 of canonicalised arguments, expiry, used flag)."
  inferred: null
---
# Confirmation

## Meaning

A pending confirmation of a write that reaches students: the preview has been shown, the call may be repeated once with the token.

## Identity

The confirmation token, stored only as its SHA-256 hash.

## Attributes

- user, tool
- hash of the canonicalised arguments (without `dry_run` and the token)
- used flag, expiry (10 minutes)

## Invariants

- It confirms exactly the user, tool and arguments of the preview, and for messages exactly the recipients shown.
- It can be used once.
