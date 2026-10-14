---
id: SM-01m4v5t39per23qtbcptdrfj4f
form: state-machine
title: "Token lifecycle"
entity:
  - E-01m4v5t1nv9vnj77m127sbym24
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3. OAuth 2.1 server"
    - "local/nitro/classes/oauth/tokens.php"
  quote: "refresh tokens carry a familyid, and reuse of a rotated token revokes the whole family"
  inferred: null
---
# Token lifecycle

## Governed lifecycle

A connection's tokens, from issue to the end of the connection.

## States

- **active**: valid, before expiry
- **rotated** (refresh token only): already exchanged for a new pair
- **expired**
- **revoked**: its connection was revoked

## Transitions

- active → rotated: the refresh token is exchanged; a new access and refresh token are issued
- rotated → revoked (whole connection): the rotated refresh token is presented again
- active → expired: access after 1 hour, refresh after 30 days
- active → revoked: the teacher revokes the connection, loses `local/nitro:use`, changes password or is deleted
