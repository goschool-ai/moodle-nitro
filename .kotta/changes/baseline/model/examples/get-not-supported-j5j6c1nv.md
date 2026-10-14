---
id: EX-01m4v5j7btz716zhqsj5j6c1nv
form: example
title: GET not supported
subjects:
  - BR-01m4v5j7btrczj6vmg8rdw60gx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Streamable HTTP
      JSON-RPC endpoint / Scenario: GET not supported
    - "local/nitro/classes/mcp/server.php · handle"
  quote: a client sends HTTP GET to the endpoint
  inferred: "The exception for appended discovery paths, read from server.php."
---
# GET not supported

## Given

nitro is enabled, and the request path is the endpoint itself, not a `/.well-known/` discovery path appended to it.

## When

a client sends HTTP GET to the endpoint

## Then

the endpoint answers HTTP 405
