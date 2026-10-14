---
id: EX-01m4v5j7bt9vss55azqd03nxma
form: example
title: Claude Code on an ephemeral port
subjects:
  - BR-01m4v5j7bts0wwhsmadh9fjtda
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Redirect URI allowlist
      / Scenario: Claude Code on an ephemeral port
  quote: 'a client uses `http://localhost:53117/callback` with the default list'
---
# Claude Code on an ephemeral port

## Given

The site keeps the default redirect URI list, which contains `http://localhost/callback`.

## When

a client uses `http://localhost:53117/callback` with the default list

## Then

the redirect URI is accepted
