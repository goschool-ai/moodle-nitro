---
id: EX-01m4v5j7btbkqvj4bdc0n19jde
form: example
title: Abandoned client
subjects:
  - BR-01m4v5j7bt8ewybvncggz1prdn
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration / Scenario: Abandoned client
    - "local/nitro/tests/task/cleanup_test.php · test_abandoned_client_is_deleted"
  quote: >-
    a dynamically registered client has obtained no token 7 days after
    registration
  inferred: "The Given, read from cleanup_test."
---
# Abandoned client

## Given

Dynamic client registration is on, a client registered through it 8 days ago has never obtained a token, and the scheduled clean-up task runs.

## When

a dynamically registered client has obtained no token 7 days after registration

## Then

it is deleted and its `client_id` no longer works
