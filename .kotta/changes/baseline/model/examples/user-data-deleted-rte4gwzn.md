---
id: EX-01m4v5j7bt4zka667frte4gwzn
form: example
title: User data deleted
subjects:
  - BR-01m4v5j7btgxmz1d0tpjagnrrx
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Declared data
      handling / Scenario: User data deleted
    - "local/nitro/tests/privacy/provider_test.php · test_export_and_delete"
  quote: a user's data is deleted through the privacy API
  inferred: "Given: read from the privacy test."
---
# User data deleted

## Given

The user has a connected AI client with access and refresh tokens, a pending authorization code and a pending confirmation.

## When

a user's data is deleted through the privacy API

## Then

that user's tokens, authorization codes and pending confirmations are deleted with it, and their grants stop working
