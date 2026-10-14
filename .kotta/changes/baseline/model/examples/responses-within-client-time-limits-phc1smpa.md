---
id: EX-01m4v5j7btdpvkp61hphc1smpa
form: example
title: Responses within client time limits
subjects:
  - BR-01m4v5j7btjrh80q3a7z21xadp
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Discovery metadata /
      Scenario: Responses within client time limits
  quote: 'a client requests a discovery, registration or token endpoint'
---
# Responses within client time limits

## Given

nitro is enabled and the site is under normal load.

## When

a client requests a discovery, registration or token endpoint

## Then

the response arrives within 2 seconds under normal load (Claude gives up after 10 seconds)
