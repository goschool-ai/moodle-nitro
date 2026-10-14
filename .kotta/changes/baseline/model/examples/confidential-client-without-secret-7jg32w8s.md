---
id: EX-01m4v5j7btcrak83pa7jg32w8s
form: example
title: Confidential client without secret
subjects:
  - BR-01m4v5j7btw45p8ys1rk5wpj5r
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token issuance /
      Scenario: Confidential client without secret
    - "local/nitro/tests/oauth/tokens_test.php · test_confidential_client_without_secret"
  quote: a client registered with a secret calls the token endpoint without it
---
# Confidential client without secret

## Given

A client registered as confidential (`client_secret_post` or `client_secret_basic`) holds a valid authorization code.

## When

a client registered with a secret calls the token endpoint without it

## Then

the response is `invalid_client`
