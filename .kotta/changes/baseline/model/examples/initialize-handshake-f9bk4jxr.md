---
id: EX-01m4v5j7btzj8sbtwcf9bk4jxr
form: example
title: Initialize handshake
subjects:
  - BR-01m4v5j7btrczj6vmg8rdw60gx
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Streamable HTTP
      JSON-RPC endpoint / Scenario: Initialize handshake
  quote: an authenticated client POSTs an `initialize` request
---
# Initialize handshake

## Given

nitro is enabled and the client holds a valid access token.

## When

an authenticated client POSTs an `initialize` request

## Then

the response contains the server name `nitro`, its version, the supported protocol version, a `tools` capability and a non-empty `instructions` string
