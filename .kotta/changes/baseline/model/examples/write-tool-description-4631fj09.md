---
id: EX-01m4v5j7btr94gyra34631fj09
form: example
title: Write tool description
subjects:
  - BR-01m4v5j7btwm1zpvjmn90fwynp
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Guidance for the AI in
      the server / Scenario: Write tool description
    - "local/nitro/tests/mcp/server_test.php · test_tools_list_matches_snapshot"
  quote: a client lists tools
---
# Write tool description

## Given

nitro is enabled, the write tools are on the allowlist, and the client holds a valid token.

## When

a client lists tools

## Then

the description of every write tool states that it changes the course and whether it requires confirmation
