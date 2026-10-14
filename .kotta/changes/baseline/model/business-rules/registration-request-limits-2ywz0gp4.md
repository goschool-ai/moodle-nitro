---
id: BR-01m4v610tbvbp1m7df2ywz0gp4
form: business-rule
title: Registration request limits
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: Registration)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (Guardrails, no per-IP rate limit)"
    - "local/nitro/oauth/register.php"
    - "local/nitro/classes/oauth/registration.php · MAX_BODY, MAX_REDIRECT_URIS"
  quote: "bodies over 16 KB are refused (413) and a client may register at most 10 redirect URIs"
  inferred: "The error code returned for too many redirect URIs, read from registration.php."
---
# Registration request limits

## Rule

The registration endpoint MUST refuse a request body larger than 16 KB with HTTP 413, and MUST refuse a registration listing more than 10 redirect URIs with `invalid_redirect_uri`, registering nothing in either case.

## Rationale

Anyone on the internet can register a client, and the design deliberately has no per-IP rate limit. The 2026-09-20 security review therefore bounded what one registration request may carry.

## Scope

Dynamic client registration only (`/local/nitro/oauth/register.php`). The admin client form has no count limit, and metadata documents are limited separately (50 KB).
