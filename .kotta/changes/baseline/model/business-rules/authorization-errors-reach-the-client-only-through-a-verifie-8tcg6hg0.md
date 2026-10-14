---
id: BR-01m4v6116fwy56rj9b8tcg6hg0
form: business-rule
title: Authorization errors reach the client only through a verified redirect URI
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (Authorization)"
    - "local/nitro/classes/oauth/authorization.php · validate, approve, deny, error_url"
    - "local/nitro/oauth/authorize.php"
  quote: "show errors on the Moodle page until then; only after a match redirect errors back with `state`. Return `iss` with the code (RFC 9207)."
  inferred: "The list of errors that stay on the page and the ones that go back to the client, read from authorization.php."
---
# Authorization errors reach the client only through a verified redirect URI

## Rule

The authorization endpoint MUST show errors on the Moodle page, without redirecting, until the client is known and the redirect URI is both registered for that client and on the allowlist. After that, every error and a user's denial SHALL go back to that redirect URI with the original `state` and the issuer as `iss`, and an approval SHALL carry `iss` together with the code.

## Rationale

The design took this order from an existing MCP OAuth server: validate the client and the redirect URI before anything else and show errors on the Moodle page until then; only after a match send errors back with `state`; and return `iss` with the code (RFC 9207).

## Scope

`/local/nitro/oauth/authorize.php`: unknown clients and unregistered or disallowed redirect URIs stay on the page; missing or plain PKCE, an unsupported response type, a foreign resource, an unknown scope and the Deny button go back to the client.
