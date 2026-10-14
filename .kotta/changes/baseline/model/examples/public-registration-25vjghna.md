---
id: EX-01m4v5j7btc7v84n7r25vjghna
form: example
title: Public registration
subjects:
  - BR-01m4v5j7bt8ewybvncggz1prdn
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration / Scenario: Public registration
  quote: >-
    a client registers with `token_endpoint_auth_method: none` and an allowed
    redirect URI
---
# Public registration

## Given

Dynamic registration is on and the never-used client cap is not reached.

## When

a client registers with `token_endpoint_auth_method: none` and an allowed redirect URI

## Then

it receives a `client_id` and no secret, and can complete the flow with PKCE alone
