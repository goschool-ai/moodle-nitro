---
id: EX-01m4v5j7btjgyqv9ey6z1sg28y
form: example
title: Teacher connects a client
subjects:
  - BR-01m4v5j7btvf34sed2vnnvhe97
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Consent screen states
      what the client can reach / Scenario: Teacher connects a client
    - "local/nitro/tests/behat/authorize.feature · The statement on where the data goes does not depend on the admin notice"
  quote: a teacher opens the consent screen for a client
  inferred: "Given: read from the Behat scenario."
---
# Teacher connects a client

## Given

nitro is switched on, the teacher holds `local/nitro:use`, and the client and its redirect URI are allowed.

## When

a teacher opens the consent screen for a client

## Then

it names the data categories the client could read, states that the data reaches the client's provider and may remain in the conversation history, and says that writes reaching students need a separate approval
