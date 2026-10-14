---
id: BR-01m4v5j7btv9nytfppj0ebv4mt
form: business-rule
title: Allowlisted tool discovery
capability: mcp-endpoint
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Allowlisted tool
      discovery
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D1 Architecture"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 Day-1 reading (Schema generation)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path (allowlist stored at install time)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes (allow or deny each tool)"
    - "local/nitro/classes/mcp/registry.php · list"
    - "local/nitro/classes/mcp/schema.php · from_description"
  quote: >-
    `tools/list` SHALL return exactly the tools on the site's allowlist, each
    with a name, a description and a JSON Schema for its input generated from
    the tool's parameter definition.
---
# Allowlisted tool discovery

## Rule

`tools/list` SHALL return exactly the tools on the site's allowlist, each with a name, a description and a JSON Schema for its input generated from the tool's parameter definition.

## Rationale

Each tool is a Moodle external function, and the MCP layer maps `tools/list` onto the allowlisted functions, generating each JSON Schema from the function's parameter description. So MCP and classic web service calls share one parameter definition. The allowlist lets an admin run a read-only site or keep grading out of the AI's reach. Upgrades add new tools to the stored allowlist because it is written at install time, and new tools would otherwise stay invisible.

## Scope

`tools/list` on `/local/nitro/mcp.php`. The allowlist is the site setting "Allowed tools" (per-tool control is an access-control rule), and only implemented tools are listed. Tool names are nitro's short names, not Moodle function names. Schemas follow the design's type mapping, and an empty parameter list is encoded as `{}`.
