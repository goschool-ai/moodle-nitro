---
id: BR-01m4v5j7btrczj6vmg8rdw60gx
form: business-rule
title: Streamable HTTP JSON-RPC endpoint
capability: mcp-endpoint
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Streamable HTTP
      JSON-RPC endpoint
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Own MCP layer (Notifications, Headers)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Goals / Non-Goals"
    - "local/nitro/classes/mcp/server.php · handle"
  quote: >-
    The plugin SHALL provide a single MCP endpoint at `/local/nitro/mcp.php`
    that accepts JSON-RPC 2.0 messages via HTTP POST according to the MCP
    Streamable HTTP transport, and SHALL answer each request
---
# Streamable HTTP JSON-RPC endpoint

## Rule

The plugin SHALL provide a single MCP endpoint at `/local/nitro/mcp.php` that accepts JSON-RPC 2.0 messages via HTTP POST according to the MCP Streamable HTTP transport, and SHALL answer each request with a single JSON response.

## Rationale

The teacher pastes one URL, and the MCP surface nitro needs is small (initialize, tools/list, tools/call, notifications), so the plugin carries its own thin adapter instead of Moodle's token-based web service stack. MCP features beyond tools, including SSE streaming, are non-goals, so every request gets one JSON response, and GET gets 405.

## Scope

`/local/nitro/mcp.php`, POST only. It implements `initialize`, `ping`, `tools/list` and `tools/call`; notifications get 202; `resources/list`, `prompts/list` and other methods get `-32601`. It sends no CORS headers and does not check `Origin`, because clients call it server to server and every request needs a bearer token. Not covered: SSE, resources, prompts, sampling.
