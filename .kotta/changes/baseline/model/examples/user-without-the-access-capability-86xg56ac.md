---
id: EX-01m4v5j7btypsftg0186xg56ac
form: example
title: User without the access capability
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: User without the access capability
    - "local/nitro/tests/behat/authorize.feature · A user without AI access gets an explanation and no code"
  quote: >-
    a logged-in user who does not hold `local/nitro:use` in any context opens an
    authorization request
---
# User without the access capability

## Given

A valid authorization request from a known client with an allowlisted redirect URI.

## When

a logged-in user who does not hold `local/nitro:use` in any context opens an authorization request

## Then

they see an explanation that AI access is not enabled for them, and no code is issued
