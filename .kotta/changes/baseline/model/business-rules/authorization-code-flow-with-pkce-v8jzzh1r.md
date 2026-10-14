---
id: BR-01m4v5j7btvq0rgyezv8jzzh1r
form: business-rule
title: Authorization code flow with PKCE
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (authorization code + PKCE, require_login, consent shows redirect host)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (keep it minimal)"
    - "openspec/specs/oauth-server/spec.md · Purpose"
    - "local/nitro/oauth/authorize.php"
    - "local/nitro/classes/oauth/authorization.php · validate"
  quote: >-
    The authorization endpoint SHALL implement the authorization code grant with
    PKCE and MUST require the `S256` code challenge method.
  inferred: "Guest handling and the no-framing headers, read from authorize.php. No source says why a resource parameter must equal the MCP endpoint URL; that is left unexplained here."
---
# Authorization code flow with PKCE

## Rule

The authorization endpoint SHALL implement the authorization code grant with PKCE and MUST require the `S256` code challenge method. The user SHALL authenticate through the normal Moodle login, see which client is asking for access (its name and redirect host), and explicitly approve it. When a `resource` parameter is sent, it MUST equal the MCP endpoint URL.

## Rationale

OAuth is mandatory because Copilot Cowork connectors do not accept API keys, and Moodle has no authorization server of its own. The design keeps the public server minimal (no implicit flow, no plain PKCE). Login goes through Moodle's `require_login()`, so every configured auth plugin and the faculty SSO work without extra code. The consent screen shows the redirect host next to the client's name because a self-declared name proves nothing.

## Scope

The authorization endpoint `/local/nitro/oauth/authorize.php`, `response_type=code` only. Only users who hold `local/nitro:use` in some context get a consent screen; guests are logged out, and the page can never be framed. What the consent text must say about data is a separate access-control rule.
