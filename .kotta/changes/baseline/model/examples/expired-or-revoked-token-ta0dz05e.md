---
id: EX-01m4v5j7bt686jhc09ta0dz05e
form: example
title: Expired or revoked token
subjects:
  - BR-01m4v5j7btyg4dkzvag2p0g170
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Bearer token
      authentication / Scenario: Expired or revoked token
  quote: 'a request carries an expired, revoked or unknown token'
---
# Expired or revoked token

## Given

nitro is enabled and the request's bearer token has expired, belongs to a revoked connection, or was never issued by nitro.

## When

a request carries an expired, revoked or unknown token

## Then

the endpoint answers HTTP 401 and executes nothing
