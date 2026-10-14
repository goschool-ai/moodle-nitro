---
id: BR-01m4v5j7bt9ahkexfas7g324tf
form: business-rule
title: Client ID Metadata Documents
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Client ID Metadata
      Documents
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (CIMD for Claude, table of clients)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 Reused from local_oauthmcp (CIMD validation)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (CIMD fetch used for SSRF)"
    - "local/nitro/classes/oauth/cimd.php · is_cimd_url, resolve, fetch, validate"
  quote: >-
    The authorization server SHALL accept a `client_id` that is an HTTPS URL on
    a host from the admin's CIMD host list (default: `claude.ai`), fetch the
    client metadata document from it,
  inferred: "The 1.5 s timeout and the 60 s cache of failed lookups, read from cimd.php."
---
# Client ID Metadata Documents

## Rule

The authorization server SHALL accept a `client_id` that is an HTTPS URL on a host from the admin's CIMD host list (default: `claude.ai`), fetch the client metadata document from it, and treat the client as a public client with the redirect URIs and name declared there. Fetched documents SHALL be cached for at most 24 hours. The server MUST NOT fetch client metadata from hosts outside the list.

## Rationale

Claude identifies itself with a Client ID Metadata Document and prefers it whenever the metadata advertises support; with dynamic registration alone it would register a new client on every fresh connection. CIMD is therefore the main path that lets a teacher connect by pasting only the MCP URL. Fetching only from the admin's host list, without following redirects, with a short timeout and a size cap, keeps the fetch from being used for server-side request forgery.

## Scope

The authorization endpoint, for a `client_id` that is an HTTPS URL on a listed host (no query, port, user info or fragment). The document must name itself as `client_id` and allow the `none` auth method; only redirect URIs on the site allowlist are kept. Fetches go through Moodle's HTTP client with the site's blocked hosts, a 1.5 s timeout and a 50 KB cap, and failed lookups are cached for 60 s. The token endpoint never fetches (see its own rule).
