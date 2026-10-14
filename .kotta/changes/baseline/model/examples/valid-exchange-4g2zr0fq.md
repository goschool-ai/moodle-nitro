---
id: EX-01m4v5j7btff82p38q4g2zr0fq
form: example
title: Valid exchange
subjects:
  - BR-01m4v5j7btw45p8ys1rk5wpj5r
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token issuance /
      Scenario: Valid exchange
    - "local/nitro/classes/oauth/authorization.php · scope"
    - "local/nitro/tests/oauth/tokens_test.php · test_code_exchange"
  quote: >-
    the client sends a valid code, the matching `code_verifier` and the same
    redirect URI
  inferred: "That an empty scope grants offline_access, read from authorization.php::scope."
---
# Valid exchange

## Given

The user approved a request whose scope included `offline_access` (or named no scope, which grants it), and the client holds the fresh authorization code within its lifetime.

## When

the client sends a valid code, the matching `code_verifier` and the same redirect URI

## Then

it receives an access token, a refresh token and `expires_in`
