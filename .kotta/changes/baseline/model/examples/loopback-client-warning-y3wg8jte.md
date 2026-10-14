---
id: EX-01m4v5j7btqceay1chy3wg8jte
form: example
title: Loopback client warning
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: Loopback client warning
    - "local/nitro/tests/oauth/authorization_test.php · test_loopback_only_client"
    - "local/nitro/tests/behat/authorize.feature · A teacher who is not logged in logs in, sees who asks, and approves"
  quote: the client's only redirect URIs are loopback addresses
  inferred: "The loopback-only client, read from authorization_test and the behat feature."
---
# Loopback client warning

## Given

A logged-in teacher with `local/nitro:use` opens an authorization request from a client registered only with loopback redirect URIs, for example `http://localhost/callback`.

## When

the client's only redirect URIs are loopback addresses

## Then

the consent screen shows an extra warning that the application runs on the user's own computer
