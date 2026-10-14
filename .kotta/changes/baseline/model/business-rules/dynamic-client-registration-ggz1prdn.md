---
id: BR-01m4v5j7bt8ewybvncggz1prdn
form: business-rule
title: Dynamic client registration
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (DCR with secrets for Copilot, Guardrails)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Accepted for the demo)"
    - "local/nitro/README.md · 4. Settings (Dynamic client registration, Limit of never-used clients)"
    - "local/nitro/classes/oauth/registration.php · handle"
    - "local/nitro/classes/oauth/clients.php · cap_reached, delete_unused"
  quote: >-
    The plugin SHALL provide an OAuth 2.0 Dynamic Client Registration endpoint
    (RFC 7591).
  inferred: "The 403 status, the default auth method, the grant/response type limits and that a cap of 0 disables it, read from registration.php and clients.php."
---
# Dynamic client registration

## Rule

The plugin SHALL provide an OAuth 2.0 Dynamic Client Registration endpoint (RFC 7591). It SHALL register public clients (`token_endpoint_auth_method: none`) and confidential clients, issuing a `client_secret` to the latter, because Microsoft 365 Copilot does not support registration without a secret. Clients that have not obtained a token within 7 days of registration SHALL be deleted. When the number of clients that never obtained a token exceeds an admin-set cap (default 1000), registrations SHALL be refused until the number drops. The admin SHALL be able to switch dynamic registration off.

## Rationale

Microsoft 365 Copilot registers through dynamic client registration and must receive a client secret. Registration is open to the internet, so it is fenced: never-used clients are deleted after 7 days and capped. There is no per-IP rate limit, because all Claude traffic comes from one Anthropic address range and would be throttled together. An institution can switch registration off and connect only clients it registers by hand, plus clients with a metadata document.

## Scope

`/local/nitro/oauth/register.php` (POST, JSON). When no method is given the RFC 7591 default `client_secret_basic` applies; only the `authorization_code` and `refresh_token` grants and the `code` response type are accepted. The cap counts only self-registered clients without a first token (a cap of 0 means no cap); the scheduled clean-up deletes them. Admin and CIMD clients are never deleted automatically. When switched off, the endpoint answers 403 and the metadata leaves it out. Accepted for the demo: never-used clients block the cap for up to 7 days.
