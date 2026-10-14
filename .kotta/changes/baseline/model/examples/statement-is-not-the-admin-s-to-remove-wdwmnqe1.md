---
id: EX-01m4v5j7btgrzexzfmwdwmnqe1
form: example
title: Statement is not the admin's to remove
subjects:
  - BR-01m4v5j7btvf34sed2vnnvhe97
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Consent screen states
      what the client can reach / Scenario: Statement is not the admin's to
      remove
    - "local/nitro/tests/behat/authorize.feature · The statement on where the data goes does not depend on the admin notice"
  quote: the admin has set no notice text
  inferred: "Given: read from the Behat scenario."
---
# Statement is not the admin's to remove

## Given

nitro is switched on, and a user who holds `local/nitro:use` opens the consent screen of an allowed client.

## When

the admin has set no notice text

## Then

the statement is still shown in full
