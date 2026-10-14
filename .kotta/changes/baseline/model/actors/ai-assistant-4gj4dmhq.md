---
id: A-01m4v5svqwf7p6qmqf4gj4dmhq
form: actor
title: "AI assistant"
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D1. Architecture: Moodle is the MCP server"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3. OAuth 2.1 server"
  quote: "Claude / Copilot Cowork — MCP (JSON-RPC over HTTPS) + Bearer token"
  inferred: "Modelled as an actor of its own: it is the external system that registers, obtains tokens and calls tools on the teacher's behalf."
---
# AI assistant

## Role

The MCP client the teacher chose: Claude (web, desktop, Claude Code), Microsoft 365 Copilot Cowork, or another MCP client such as the GoSchool assistant. It is outside Moodle and run by its own provider.

## Goals

Act on the teacher's request inside Moodle with exactly the teacher's permissions.

## Responsibilities

Identifies itself (Client ID Metadata Document, dynamic registration or a hand-registered client), runs the authorization code flow with PKCE, calls the allowlisted tools with the teacher's bearer token, shows previews to the teacher word for word and repeats a confirmed call only after the teacher approves.
