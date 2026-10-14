---
id: EX-01m4v6188g01d02rhfes5d5h76
form: example
title: Refresh from a metadata URL never seen before
subjects:
  - BR-01m4v610mdsezkxa7hdvk3h1g2
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/security_review_test.php · test_cimd_url_with_query_and_token_endpoint_never_fetches"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Refresh from a metadata URL never seen before

## Given

`claude.ai` is on the CIMD host list, and the site holds no code, connection or cached document for the client ID `https://claude.ai/oauth/mcp-oauth-client-metadata`.

## When

a token request with that client ID and a refresh token arrives

## Then

the response is HTTP 401 `invalid_client`, and the site sends no request to claude.ai
