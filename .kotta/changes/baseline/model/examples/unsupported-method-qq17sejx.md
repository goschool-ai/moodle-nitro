---
id: EX-01m4v5j7bt1patc8q0qq17sejx
form: example
title: Unsupported method
subjects:
  - BR-01m4v5j7btrczj6vmg8rdw60gx
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Streamable HTTP
      JSON-RPC endpoint / Scenario: Unsupported method
    - "local/nitro/tests/mcp/server_test.php · test_unknown_method"
  quote: the client calls a JSON-RPC method the server does not implement
---
# Unsupported method

## Given

An authenticated client.

## When

the client calls a JSON-RPC method the server does not implement

## Then

the response is a JSON-RPC error with code `-32601`
