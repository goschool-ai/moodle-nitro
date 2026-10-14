---
id: EX-01m4v619emf1m50fdg5dewf9nb
form: example
title: Unknown protocol version in initialize
subjects:
  - BR-01m4v611rpjb0y1hnprnwvsc7w
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/mcp/server_test.php · test_initialize_with_unknown_version_gets_latest"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Unknown protocol version in initialize

## Given

An authenticated client.

## When

it sends `initialize` with `protocolVersion` `2024-01-01`

## Then

the result's `protocolVersion` is `2025-11-25`, the newest version nitro supports
