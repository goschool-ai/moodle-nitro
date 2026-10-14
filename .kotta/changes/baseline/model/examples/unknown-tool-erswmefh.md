---
id: EX-01m4v5j7btfs11whpterswmefh
form: example
title: Unknown tool
subjects:
  - BR-01m4v5j7bt31c41e3fpbthvp6m
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Tool invocation /
      Scenario: Unknown tool
    - "local/nitro/tests/mcp/server_test.php · test_unknown_tool"
  quote: a client calls a tool name that is not on the allowlist
---
# Unknown tool

## Given

An authenticated client, and a tool name that is unticked in Allowed tools or is not a nitro tool at all.

## When

a client calls a tool name that is not on the allowlist

## Then

the response is a JSON-RPC error with code `-32602`
