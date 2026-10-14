---
id: E-01m4v5t1ez6krttwrmqscpp4dc
form: entity
title: "Connection"
used_by:
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
  - UC-01m4v5sx33f7734ss0egw7rdx5
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/db/install.xml · local_nitro_grant"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review"
  quote: "A user's approval of one client; what the connections page lists and revokes"
  inferred: null
---
# Connection

## Meaning

A teacher's approval of one AI client: what the Connected AI tools page lists and what revoking removes.

## Identity

The pair of user and client ID.

## Attributes

- user, client ID, client name, redirect host, scope
- last use, created

## Invariants

- Revoking it deletes all its tokens at once.
- Losing `local/nitro:use`, a password change or account deletion revokes it.
