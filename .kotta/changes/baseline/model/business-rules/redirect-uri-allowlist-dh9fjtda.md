---
id: BR-01m4v5j7bts0wwhsmadh9fjtda
form: business-rule
title: Redirect URI allowlist
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/oauth-server/spec.md · Requirement: Redirect URI allowlist'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (Guardrails)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (Loopback)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (malicious client with misleading name)"
    - "local/nitro/README.md · 4. Settings (Allowed redirect URIs), 6. Connecting an AI client"
    - "local/nitro/classes/oauth/redirect_uris.php · matches, is_allowed"
  quote: The admin SHALL configure the list of allowed redirect URIs.
  inferred: "The [::1] loopback host and that a URI removed from the list later fails at authorization, read from redirect_uris.php and authorization_test::test_redirect_uri_removed_from_allowlist_later."
---
# Redirect URI allowlist

## Rule

The admin SHALL configure the list of allowed redirect URIs. The default list SHALL contain `https://claude.ai/api/mcp/auth_callback`, `https://claude.com/api/mcp/auth_callback`, `https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect`, and the loopback redirects `http://localhost/callback` and `http://127.0.0.1/callback`. Loopback entries MUST match any port. Every client registration path (CIMD, dynamic, admin) MUST reject redirect URIs outside the list.

## Rationale

Client registration is open to the internet, so the allowlist is the main guardrail: a client registered under a misleading name can still only send codes back to a known callback, and a flood of registrations can only create unusable clients that the clean-up removes. Loopback entries match any port because native clients such as Claude Code listen on an ephemeral port.

## Scope

The site setting "Allowed redirect URIs", applied to dynamic registration, the admin client form, the redirect URIs taken from a CIMD document, and every authorization request (a URI removed later stops working there too). Matching is exact, except that `http` loopback URIs on `localhost`, `127.0.0.1` or `[::1]` ignore the port; URIs with a fragment or user info never match. A client outside the defaults, such as the GoSchool assistant, needs its callback added by the admin first.
