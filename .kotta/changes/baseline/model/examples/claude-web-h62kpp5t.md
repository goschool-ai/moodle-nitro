---
id: EX-01m4v5j7btbtvygn4xh62kpp5t
form: example
title: Claude web
subjects:
  - BR-01m4v5j7bts0wwhsmadh9fjtda
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Redirect URI allowlist
      / Scenario: Claude web
  quote: >-
    a client uses `https://claude.ai/api/mcp/auth_callback` with the default
    list
---
# Claude web

## Given

The site keeps the default redirect URI list.

## When

a client uses `https://claude.ai/api/mcp/auth_callback` with the default list

## Then

the redirect URI is accepted
