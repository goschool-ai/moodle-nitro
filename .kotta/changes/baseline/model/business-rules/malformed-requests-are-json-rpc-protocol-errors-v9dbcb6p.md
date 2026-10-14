---
id: BR-01m4v6127a9gy8r5gvv9dbcb6p
form: business-rule
title: Malformed requests are JSON-RPC protocol errors
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Day-1 reading (Errors: JSON-RPC errors stay for protocol faults only)"
    - "local/nitro/classes/mcp/server.php · handle"
  quote: "JSON-RPC errors stay for protocol faults only (parse error `-32700`, invalid request `-32600`, unknown method `-32601`, unknown tool `-32602`)."
  inferred: "The HTTP 400 status and that batches are refused, read from server.php; no source says why batches are not supported."
---
# Malformed requests are JSON-RPC protocol errors

## Rule

The endpoint MUST answer a body that is not a JSON object with HTTP 400 and JSON-RPC error `-32700`, and a JSON-RPC batch or a message without `"jsonrpc": "2.0"` and a string `method` with HTTP 400 and `-32600`. In every such case it MUST execute nothing.

## Rationale

The design keeps JSON-RPC errors for protocol faults only: parse error `-32700`, invalid request `-32600`, unknown method `-32601` and unknown tool `-32602`. Failures inside a tool are tool results instead.

## Scope

Authenticated POST requests to `/local/nitro/mcp.php`; a request without a valid token gets 401 first. Unknown methods and unknown tools are covered by their own rules.
