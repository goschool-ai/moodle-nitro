---
id: EX-01m4v5j7bt1718xwka98fze9jb
form: example
title: Foreign resource
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: Foreign resource
    - "local/nitro/tests/oauth/authorization_test.php · test_client_error"
  quote: >-
    an authorization request carries a `resource` other than the MCP endpoint
    URL
  inferred: "That the error is redirected to the client because the client and redirect URI are valid, read from authorization.php."
---
# Foreign resource

## Given

A logged-in user with `local/nitro:use` opens an authorization request from a known client with an allowlisted redirect URI registered for it, so errors go back to the client.

## When

an authorization request carries a `resource` other than the MCP endpoint URL

## Then

the request is rejected with `invalid_target`
