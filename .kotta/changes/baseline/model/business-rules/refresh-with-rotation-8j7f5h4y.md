---
id: BR-01m4v5j7btkhbb5ah08j7f5h4y
form: business-rule
title: Refresh with rotation
capability: oauth-server
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/oauth-server/spec.md · Requirement: Refresh with rotation'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (Token: familyid, capability re-check)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: CIMD; Accepted for the demo)"
    - "local/nitro/classes/oauth/tokens.php · refresh"
  quote: >-
    The token endpoint SHALL accept a refresh token to issue a new access token
    and a new refresh token in the same response, and MUST invalidate the used
    refresh token.
---
# Refresh with rotation

## Rule

The token endpoint SHALL accept a refresh token to issue a new access token and a new refresh token in the same response, and MUST invalidate the used refresh token. Refresh tokens MUST expire after at most 30 days. An invalid refresh token MUST be answered with `invalid_grant`.

## Rationale

Refresh tokens carry a family: if a rotated token comes back, someone else holds a copy, so the whole connection is revoked. The access capability is checked again on every refresh. A client known by its metadata URL is accepted from its existing grant without fetching or caching the document, because relying on the cache alone threw Claude out after every cache purge (each deploy purges caches).

## Scope

`token.php` with `grant_type=refresh_token`, only for connections granted `offline_access`. The token must belong to the presenting client, and the user must still be active and hold `local/nitro:use`, otherwise the connection is revoked. Accepted for the demo: there is no grace period, so a client that refreshes twice in parallel loses its connection. There is no RFC 7009 revocation endpoint.
