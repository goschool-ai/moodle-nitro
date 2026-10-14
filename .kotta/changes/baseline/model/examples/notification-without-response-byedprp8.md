---
id: EX-01m4v5j7bte9y9kr1zbyedprp8
form: example
title: Notification without response
subjects:
  - BR-01m4v5j7btrczj6vmg8rdw60gx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Streamable HTTP
      JSON-RPC endpoint / Scenario: Notification without response
    - "local/nitro/classes/mcp/server.php · handle"
  quote: >-
    the client POSTs a JSON-RPC notification (no `id`), such as
    `notifications/initialized`
  inferred: "That notifications are only acknowledged after the token check, read from server.php."
---
# Notification without response

## Given

The client holds a valid access token, because notifications go through the same token check as requests.

## When

the client POSTs a JSON-RPC notification (no `id`), such as `notifications/initialized`

## Then

the endpoint answers HTTP 202 with an empty body
