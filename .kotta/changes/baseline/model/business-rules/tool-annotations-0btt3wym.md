---
id: BR-01m4v61205akg1k1wb0btt3wym
form: business-rule
title: Tool annotations
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Day-1 reading (Tool annotations)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Open Questions (Cowork reads MCP tool annotations)"
    - "local/nitro/classes/local/tools.php · annotations, TITLES, READ, CONFIRM, CONFIRM_ONLY"
    - "local/nitro/classes/mcp/registry.php · list"
  quote: "Tool annotations: `readOnlyHint` for read tools, `destructiveHint` for the three confirmation tools, set explicitly per tool rather than guessed from the function name as eledia does."
  inferred: "That send_feedback is also marked destructive, and the protocol-version condition, read from tools.php and registry.php."
---
# Tool annotations

## Rule

For clients on a protocol version newer than 2025-03-26, every tool in `tools/list` SHALL carry annotations: a human-readable `title`, `readOnlyHint: true` exactly for the tools that only read, and `destructiveHint: true` exactly for the tools that need a confirmed preview because they reach students or leave Moodle. The annotations MUST be set explicitly per tool, not guessed from the tool's name.

## Rationale

The design sets tool annotations explicitly per tool rather than guessing them from the function name, as another Moodle MCP plugin does. Copilot Cowork reads `readOnlyHint`, `destructiveHint` and `title` to decide its own confirmation prompts.

## Scope

`tools/list`, for every allowlisted tool. `idempotentHint` and `openWorldHint` are sent as well. Clients on 2025-03-26 get no annotations.
