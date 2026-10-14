---
id: BR-01m4v611rpjb0y1hnprnwvsc7w
form: business-rule
title: Protocol version negotiation
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Day-1 reading (Protocol version)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3a What Claude actually does (400 on MCP-Protocol-Version 2026-07-28)"
    - "local/nitro/classes/mcp/server.php · PROTOCOL_VERSIONS, initialize, handle"
  quote: "negotiate from `initialize.params.protocolVersion` against a supported list (2025-11-25, 2025-06-18, 2025-03-26), answer with the latest when unknown; reject an unsupported `MCP-Protocol-Version` header with 400."
  inferred: "That requests without the header are treated as 2025-03-26, read from server.php."
---
# Protocol version negotiation

## Rule

The endpoint SHALL support the MCP protocol versions 2025-11-25, 2025-06-18 and 2025-03-26. `initialize` SHALL answer with the version the client requested when that version is supported, and with the newest supported version otherwise. A request whose `MCP-Protocol-Version` header names an unsupported version MUST be answered with HTTP 400.

## Rationale

The design negotiates the version from the client's `initialize` request against a supported list, unlike the existing Moodle MCP plugin, which hard-codes 2025-03-26. The MCP specification requires 400 for an unsupported version header. The 2026-09-20 probe showed that Claude Code opens with a newer version, gets 400, falls back to a supported version and then works.

## Scope

`/local/nitro/mcp.php`. A request without the header is treated as 2025-03-26 when results are shaped (no `structuredContent`, no annotations). Support for 2026-07-28 is a pilot item.
