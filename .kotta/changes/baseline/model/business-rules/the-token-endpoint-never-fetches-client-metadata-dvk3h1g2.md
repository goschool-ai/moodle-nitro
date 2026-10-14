---
id: BR-01m4v610mdsezkxa7hdvk3h1g2
form: business-rule
title: The token endpoint never fetches client metadata
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: CIMD)"
    - "local/nitro/classes/oauth/tokens.php · client_for_token_request"
    - "local/nitro/classes/oauth/cimd.php · is_cimd_url"
  quote: "the token endpoint never fetches a document, so unauthenticated requests cannot make the site send requests to claude.ai"
  inferred: "The invalid_client answer for a URL client never seen before, read from tokens.php and security_review_test."
---
# The token endpoint never fetches client metadata

## Rule

The token endpoint MUST NOT fetch a Client ID Metadata Document. It SHALL accept a client ID that is a URL only when that client already holds an authorization code or a connection on this site, or when its validated document is still cached. Otherwise it MUST answer `invalid_client`. A client ID URL with a query string MUST NOT be treated as a metadata document URL.

## Rationale

The token endpoint is unauthenticated, so if it fetched documents, anyone could make the site send requests to claude.ai. Relying on the cache alone broke every refresh after a cache purge (each deploy purges caches): Claude was thrown out with a 401 and a re-authorization prompt. A client that already holds a code or a connection is therefore accepted as the public client it is, without fetching or caching anything.

## Scope

`/local/nitro/oauth/token.php`, for both grant types, when the client ID looks like a URL; the query-string refusal applies wherever a client ID is checked for being a metadata URL. The authorization endpoint still fetches documents under the Client ID Metadata Documents rule.
