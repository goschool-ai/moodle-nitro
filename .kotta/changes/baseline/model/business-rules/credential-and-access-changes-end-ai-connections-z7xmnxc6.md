---
id: BR-01m4v610earfshm4bfz7xmnxc6
form: business-rule
title: Credential and access changes end AI connections
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: tokens stop working ..., password change or account deletion revokes)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (Token: capability re-checked on every refresh)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Accepted for the demo)"
    - "local/nitro/classes/observer.php · revoke_user_grants"
    - "local/nitro/db/events.php"
    - "local/nitro/classes/oauth/tokens.php · user_is_active, exchange_code, refresh"
    - "local/nitro/classes/mcp/server.php · handle"
  quote: "Tokens stop working when the user's auth method is disabled and during maintenance mode; a password change or account deletion revokes all the user's connections."
  inferred: "The no-login case, where each check sits, and that an access token already issued survives a withdrawn capability until it expires, read from tokens.php, observer.php and server.php."
---
# Credential and access changes end AI connections

## Rule

A user's AI connections MUST end when their credentials or access change. A password change or account deletion SHALL revoke all of the user's connections and their tokens. Access tokens MUST be refused while the user's authentication method is disabled or set to no-login, and MCP requests MUST be refused while the site is in maintenance mode. An authorization code MUST NOT be exchanged, and a refresh MUST revoke the connection, when the user no longer holds `local/nitro:use` in any context.

## Rationale

The 2026-09-20 security review made tokens stop working when the user's auth method is disabled and during maintenance mode, and made a password change or account deletion revoke all the user's connections, as a password change ends their Moodle sessions. The design also re-checks the access capability on every refresh, and losing it revokes the whole token family.

## Scope

Observers on Moodle's password-updated and user-deleted events, the token checks on every MCP request, the code exchange and the refresh. An access token already issued keeps working until it expires (at most 1 hour) after the capability is withdrawn, but every tool still checks the gate per course. Forced password change and an unaccepted site policy are not checked (accepted for the demo).
