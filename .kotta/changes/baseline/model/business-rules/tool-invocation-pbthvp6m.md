---
id: BR-01m4v5j7bt31c41e3fpbthvp6m
form: business-rule
title: Tool invocation
capability: mcp-endpoint
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/mcp-endpoint/spec.md · Requirement: Tool invocation'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Day-1 reading (Errors, Output, implementation notes)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Goals / Non-Goals (same code path for MCP and web services)"
    - "local/nitro/classes/mcp/dispatcher.php · call, message"
    - "local/nitro/classes/mcp/server.php · call_tool"
  quote: >-
    `tools/call` SHALL validate the arguments against the tool's parameter
    definition, run the tool, and return its result as MCP tool content.
---
# Tool invocation

## Rule

`tools/call` SHALL validate the arguments against the tool's parameter definition, run the tool, and return its result as MCP tool content. Failures inside a tool SHALL be returned as a tool result with `isError: true` and a human-readable message, so the AI can react to them.

## Rationale

Exceptions inside a tool (invalid parameters, missing capability, Moodle errors) become a result with `isError: true` and a plain message, so the model can correct itself. Most clients show a JSON-RPC error as a failed call, so JSON-RPC errors are kept for protocol faults only. Calls go through Moodle's external-function validation, so MCP and web service calls share one code path. nitro's own dispatcher keeps the detail that names an invalid parameter, which Moodle's core drops unless developer debugging is on.

## Scope

`tools/call` on `/local/nitro/mcp.php`, for allowlisted, implemented tools. The per-course access gate is checked before the tool runs. A transaction left open by a failing tool is rolled back. Every outcome is logged as an audit event. An unknown tool or non-object arguments get JSON-RPC `-32602`. Results carry JSON text plus `structuredContent` for protocol versions after 2025-03-26.
