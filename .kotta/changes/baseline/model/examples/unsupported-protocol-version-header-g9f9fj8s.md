---
id: EX-01m4v619nywdqn8phfg9f9fj8s
form: example
title: Unsupported protocol version header
subjects:
  - BR-01m4v611rpjb0y1hnprnwvsc7w
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/mcp/server_test.php · test_unsupported_protocol_header"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Unsupported protocol version header

## Given

An authenticated client.

## When

it sends any request with the header `MCP-Protocol-Version: 1999-01-01`

## Then

the endpoint answers HTTP 400 and runs nothing
