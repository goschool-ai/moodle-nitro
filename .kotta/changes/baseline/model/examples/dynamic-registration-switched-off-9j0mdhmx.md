---
id: EX-01m4v5j7bt5d4mpg1n9j0mdhmx
form: example
title: Dynamic registration switched off
subjects:
  - BR-01m4v5j7bt8ewybvncggz1prdn
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration / Scenario: Dynamic registration switched off
    - "local/nitro/tests/oauth/registration_test.php · test_switched_off"
  quote: the admin switches dynamic registration off
---
# Dynamic registration switched off

## Given

Dynamic registration is on, and clients registered dynamically or by the admin already exist.

## When

the admin switches dynamic registration off

## Then

the registration endpoint rejects every request, it is no longer advertised in the metadata, and existing clients keep working
