---
id: EX-01m4v5j7bt0yk4y8732s8d91xz
form: example
title: Teacher disconnects Claude
subjects:
  - BR-01m4v5j7bt7gnewzbmexq090x9
  - UC-01m4v5sx33f7734ss0egw7rdx5
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Users see and revoke
      their connections / Scenario: Teacher disconnects Claude
    - "local/nitro/tests/behat/connections.feature · A teacher disconnects Claude from their profile"
  quote: a teacher revokes the Claude entry on their connections page
---
# Teacher disconnects Claude

## Given

A teacher has approved Claude, so their Connected AI tools page lists a Claude connection with valid tokens.

## When

a teacher revokes the Claude entry on their connections page

## Then

the next MCP request with any token of that grant is answered with HTTP 401, and the client has to go through authorization again
