---
id: EX-01m4v5j7btx631z3r55rzf7tc2
form: example
title: Missing token
subjects:
  - BR-01m4v5j7btyg4dkzvag2p0g170
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Bearer token
      authentication / Scenario: Missing token
  quote: 'a request arrives without an `Authorization: Bearer` header'
---
# Missing token

## Given

nitro is enabled and the site is not in maintenance mode.

## When

a request arrives without an `Authorization: Bearer` header

## Then

the endpoint answers HTTP 401 with a `WWW-Authenticate` header whose `resource_metadata` parameter points to the protected resource metadata document
