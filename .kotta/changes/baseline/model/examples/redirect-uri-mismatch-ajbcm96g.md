---
id: EX-01m4v5j7btjd786q9pajbcm96g
form: example
title: Redirect URI mismatch
subjects:
  - BR-01m4v5j7btm6qyqm6d798d1zpa
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Admin client
      registration / Scenario: Redirect URI mismatch
    - "local/nitro/tests/oauth/authorization_test.php · test_local_error"
  quote: >-
    an authorization request uses a redirect URI that is not registered for the
    client
---
# Redirect URI mismatch

## Given

A client is registered, by the admin or dynamically, with a fixed set of redirect URIs.

## When

an authorization request uses a redirect URI that is not registered for the client

## Then

the request is rejected with an error page and no redirect happens
