---
id: EX-01m4v5j7bt3ekhdssky9emfh9f
form: example
title: Missing or plain PKCE
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: Missing or plain PKCE
    - "local/nitro/tests/oauth/authorization_test.php · test_client_error"
  quote: >-
    an authorization request has no `code_challenge` or uses
    `code_challenge_method=plain`
  inferred: "That client and redirect URI are valid, so the error goes back to the client, read from authorization.php."
---
# Missing or plain PKCE

## Given

The request names a known client and a redirect URI that is registered for it and on the allowlist.

## When

an authorization request has no `code_challenge` or uses `code_challenge_method=plain`

## Then

the request is rejected
