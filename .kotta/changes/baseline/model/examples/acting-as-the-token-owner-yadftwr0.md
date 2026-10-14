---
id: EX-01m4v5j7bt40ejqnkhyadftwr0
form: example
title: Acting as the token owner
subjects:
  - BR-01m4v5j7btyg4dkzvag2p0g170
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Bearer token
      authentication / Scenario: Acting as the token owner
  quote: a request carries a valid token of user U
---
# Acting as the token owner

## Given

nitro is enabled and user U, an active Moodle user, holds an unexpired access token from a connection they approved.

## When

a request carries a valid token of user U

## Then

every tool call in that request runs with U's identity and U's Moodle capabilities
