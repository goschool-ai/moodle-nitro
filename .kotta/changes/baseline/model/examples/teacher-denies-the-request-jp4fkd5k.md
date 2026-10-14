---
id: EX-01m4v618ty2kytjrp7jp4fkd5k
form: example
title: Teacher denies the request
subjects:
  - BR-01m4v6116fwy56rj9b8tcg6hg0
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/behat/authorize.feature · A teacher denies the request"
    - "local/nitro/tests/oauth/authorization_test.php · test_user_denies"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Teacher denies the request

## Given

A logged-in teacher with `local/nitro:use` sees the consent screen for a known client with an allowlisted redirect URI, from a request with `state=xyz`.

## When

the teacher presses Deny

## Then

they are redirected to the client's redirect URI with `error=access_denied`, `state=xyz` and `iss`, and no code is issued
