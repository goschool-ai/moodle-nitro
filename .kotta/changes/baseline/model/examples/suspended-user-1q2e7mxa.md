---
id: EX-01m4v5j7bt1tp1bqbe1q2e7mxa
form: example
title: Suspended user
subjects:
  - BR-01m4v5j7btgx9v6ccgw8azb6hg
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token secrecy and
      validity / Scenario: Suspended user
    - "local/nitro/tests/oauth/tokens_test.php · test_suspended_user"
  quote: a user with a valid access token is suspended in Moodle
---
# Suspended user

## Given

A user holds a valid access token from a connection they approved.

## When

a user with a valid access token is suspended in Moodle

## Then

the next MCP request with that token is answered with HTTP 401
