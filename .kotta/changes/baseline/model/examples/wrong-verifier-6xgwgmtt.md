---
id: EX-01m4v5j7btem5qc7qh6xgwgmtt
form: example
title: Wrong verifier
subjects:
  - BR-01m4v5j7btw45p8ys1rk5wpj5r
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token issuance /
      Scenario: Wrong verifier
    - "local/nitro/tests/oauth/tokens_test.php · test_bad_exchange"
  quote: the `code_verifier` does not match the code challenge
---
# Wrong verifier

## Given

A client holds a valid authorization code issued for an S256 code challenge.

## When

the `code_verifier` does not match the code challenge

## Then

the response is `invalid_grant` and the code is invalidated
