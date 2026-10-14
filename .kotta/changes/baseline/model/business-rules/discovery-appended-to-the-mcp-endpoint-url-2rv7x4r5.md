---
id: BR-01m4v611jskt85yswm2rv7x4r5
form: business-rule
title: Discovery appended to the MCP endpoint URL
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D4 (mcp.php also answers the appended form from PATH_INFO)"
    - "local/nitro/classes/mcp/server.php · handle"
    - "local/nitro/classes/oauth/metadata.php · for_path"
  quote: "`mcp.php` also answers the appended form from `PATH_INFO`"
  inferred: "That no token is needed and which paths map to which document, read from server.php and metadata.php."
---
# Discovery appended to the MCP endpoint URL

## Rule

The MCP endpoint SHALL answer an HTTP GET for a `/.well-known/` path appended to its own URL (for example `/local/nitro/mcp.php/.well-known/openid-configuration`) with the matching discovery document and without asking for a token: the protected resource metadata for `oauth-protected-resource` paths, and the authorization server metadata for every other path.

## Rationale

Some clients look for discovery documents appended to the MCP URL rather than to the issuer; an existing MCP OAuth server reported that ChatGPT does this. The design therefore has `mcp.php` answer the appended form as well.

## Scope

GET requests to `/local/nitro/mcp.php` whose path continues with `/.well-known/`, while nitro is enabled. Every other GET still gets 405.
