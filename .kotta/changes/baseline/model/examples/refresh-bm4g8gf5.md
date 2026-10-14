---
id: EX-01m4v5j7btqr5ybs7cbm4g8gf5
form: example
title: Refresh
subjects:
  - BR-01m4v5j7btkhbb5ah08j7f5h4y
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Refresh with rotation /
      Scenario: Refresh
    - "local/nitro/tests/oauth/tokens_test.php · test_refresh_rotates"
  quote: the client presents a valid refresh token
---
# Refresh

## Given

A connection granted with `offline_access` holds an unused, unexpired refresh token, and its user is still active and holds `local/nitro:use`.

## When

the client presents a valid refresh token

## Then

it receives a new access token and a new refresh token, and the old refresh token no longer works
