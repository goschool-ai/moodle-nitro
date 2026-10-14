---
id: EX-01m4v5j7bt93h8jdfbc40ck242
form: example
title: Code reuse
subjects:
  - BR-01m4v5j7btw45p8ys1rk5wpj5r
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token issuance /
      Scenario: Code reuse
  quote: an authorization code is presented a second time
---
# Code reuse

## Given

A client has already exchanged an authorization code for tokens once.

## When

an authorization code is presented a second time

## Then

the response is `invalid_grant`
