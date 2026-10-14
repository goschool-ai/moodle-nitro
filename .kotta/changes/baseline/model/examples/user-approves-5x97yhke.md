---
id: EX-01m4v5j7bt1xpsfgya5x97yhke
form: example
title: User approves
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: User approves
    - "local/nitro/tests/behat/authorize.feature · A teacher who is not logged in logs in, sees who asks, and approves"
  quote: 'a logged-in user who holds `local/nitro:use` approves the consent screen'
---
# User approves

## Given

A logged-in user opens a valid authorization request with a `state` value, from a known client with an allowlisted redirect URI.

## When

a logged-in user who holds `local/nitro:use` approves the consent screen

## Then

they are redirected to the client's redirect URI with a single-use authorization code and the original `state`
