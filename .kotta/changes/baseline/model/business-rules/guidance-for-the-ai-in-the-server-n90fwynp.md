---
id: BR-01m4v5j7btwm1zpvjmn90fwynp
form: business-rule
title: Guidance for the AI in the server
capability: mcp-endpoint
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Guidance for the AI in
      the server
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes (no client-side skill packages)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (without skills the AI may chain quiz tools wrongly)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Open Questions (Cowork mcpToolDescription)"
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
  quote: >-
    Because no client-side skill is shipped, the server SHALL carry the usage
    guidance itself: the `instructions` field SHALL describe the typical
    workflows (find the course, preview before writing, import questions
---
# Guidance for the AI in the server

## Rule

Because no client-side skill is shipped, the server SHALL carry the usage guidance itself: the `instructions` field SHALL describe the typical workflows (find the course, preview before writing, import questions before building a quiz), and each tool description SHALL state when to use the tool and what it changes.

## Rationale

This change ships no client-side skill package: teacher- and course-specific rules stay in the teacher's own client. Without skills, the AI may chain the quiz tools in the wrong order, so the design's mitigation is rich server `instructions`, detailed tool descriptions, and coarse, forgiving tools whose errors say what to do next.

## Scope

The `instructions` string returned by `initialize` and the `description` of every tool in `tools/list`. Copilot Cowork packages the same `tools/list` result as its `mcpToolDescription`. It does not cover teacher- or course-specific rules (course constants, feedback style), which stay in the teacher's own client.
