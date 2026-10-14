---
id: EX-01m4v5j7bt5nce33pg9msat0s0
form: example
title: Sandbox notice
subjects:
  - BR-01m4v5j7btfvmc36fr4thmn9pm
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Consent screen notice
      / Scenario: Sandbox notice
    - "local/nitro/tests/behat/authorize.feature · A teacher who is not logged in logs in, sees who asks, and approves"
  quote: the admin has set a notice and a user opens the consent screen
  inferred: "Given: read from the Behat background."
---
# Sandbox notice

## Given

nitro is switched on, and the user holds `local/nitro:use` and starts connecting a client whose redirect URI is allowed.

## When

the admin has set a notice and a user opens the consent screen

## Then

the notice is shown above the approve button
