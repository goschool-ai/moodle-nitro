---
id: G-01m4v5j7bte85hyxac31g4x4n1
form: goal
title: Purpose of mcp-endpoint
measured_by:
  - EX-01m4v5j7btfndrnnh8kzk28f7s
serves:
  - G-01m4v5saj3ph52x7vag1y62efr
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - openspec/specs/mcp-endpoint/spec.md · Purpose
  quote: >-
    Exposes Moodle as an MCP server so that an AI assistant (Claude, Copilot
    Cowork) can discover and call course-management tools directly, without an
    intermediary service.
  inferred: "Context and baseline/target written from the archived nitro-demo design and proposal."
---
# Purpose of mcp-endpoint

## Outcome

Exposes Moodle as an MCP server so that an AI assistant (Claude, Copilot Cowork) can discover and call course-management tools directly, without an intermediary service.

## Context

Claude and Copilot both speak MCP; Moodle itself becomes the MCP server so no intermediary service carries student data.

## Baseline and target

Baseline: no MCP server in Moodle that can write content. Target: a standard MCP endpoint with allowlisted tools that Claude connects to with the URL alone.
