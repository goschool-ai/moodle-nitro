---
id: BR-01m4v5j7btyg4dkzvag2p0g170
form: business-rule
title: Bearer token authentication
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Bearer token
      authentication
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D1 Architecture"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2 (Headers, implementation notes)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "local/nitro/classes/mcp/server.php · handle"
  quote: >-
    The endpoint MUST reject every request that does not carry a valid OAuth
    access token issued by this plugin for this endpoint, and MUST execute
    authenticated requests as the Moodle
  inferred: "The 503 answer during maintenance mode before the token check, read from server.php."
---
# Bearer token authentication

## Rule

The endpoint MUST reject every request that does not carry a valid OAuth access token issued by this plugin for this endpoint, and MUST execute authenticated requests as the Moodle user the token belongs to.

## Rationale

Moodle itself is the MCP server with no service in between, and the bearer token is what ties each request to one teacher, so every tool runs with exactly that teacher's Moodle permissions. nitro uses its own OAuth tokens rather than Moodle web service tokens, which have to be copied by hand and carry a narrow function list.

## Scope

Every POST to `/local/nitro/mcp.php`. The token is read from the `Authorization` header (also where PHP-FPM or Apache moved it), never from the query string, and the endpoint uses no session or cookies. Discovery documents appended under `/.well-known/` need no token. While the plugin is off or the site is in maintenance mode, requests get 503 before any token is checked.
