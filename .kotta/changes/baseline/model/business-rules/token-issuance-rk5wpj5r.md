---
id: BR-01m4v5j7btw45p8ys1rk5wpj5r
form: business-rule
title: Token issuance
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/oauth-server/spec.md · Requirement: Token issuance'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (short lifetimes, no plain PKCE)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (Authorization, Token)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: single use under a lock; Basic and body refused)"
    - "local/nitro/classes/oauth/tokens.php · handle, exchange_code"
    - "local/nitro/oauth/token.php"
  quote: >-
    The token endpoint SHALL accept `application/x-www-form-urlencoded` requests
    and exchange a valid authorization code and matching `code_verifier` for an
    access token bound to the user, the client and the MCP endpoint,
  inferred: "The actual lifetimes (5-minute code, 1-hour access token) and the body-only parameter rule, read from authorization.php, tokens.php and token.php. No source says why RFC 6749 error codes are required beyond following the standard."
---
# Token issuance

## Rule

The token endpoint SHALL accept `application/x-www-form-urlencoded` requests and exchange a valid authorization code and matching `code_verifier` for an access token bound to the user, the client and the MCP endpoint, plus a refresh token when `offline_access` was granted. Authorization codes MUST be single-use and expire within 10 minutes. Access tokens MUST expire within 1 hour. Errors MUST use RFC 6749 error codes.

## Rationale

Short lifetimes and single-use codes are part of keeping a public OAuth server minimal. Confidential clients, such as Copilot, have to prove their secret. Codes are deleted on redemption, and they are claimed under a lock so that two parallel requests cannot both succeed.

## Scope

`/local/nitro/oauth/token.php`, POST, with parameters taken from the form body only, never from the query string. Codes live 5 minutes and access tokens 1 hour. A code is deleted the moment it is presented, so a failed exchange also burns it. The user must still be active and hold `local/nitro:use`. A client authenticates in the body or with HTTP Basic, never both. Refresh is a separate rule.
