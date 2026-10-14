---
id: EX-01m4v5j7bter3x2jhrb8ashkrf
form: example
title: Cap reached
subjects:
  - BR-01m4v5j7bt8ewybvncggz1prdn
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration / Scenario: Cap reached
    - "local/nitro/tests/oauth/registration_test.php · test_cap_reached"
  quote: the number of never-used clients is at the cap
---
# Cap reached

## Given

Dynamic registration is on and the admin's cap of never-used clients (default 1000) is set; that many self-registered clients have not obtained a token yet.

## When

the number of never-used clients is at the cap

## Then

a new registration is refused with HTTP 503 and the admin sees a warning on the settings page
