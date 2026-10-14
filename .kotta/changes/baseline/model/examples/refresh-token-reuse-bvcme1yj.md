---
id: EX-01m4v5j7btet29hsxkbvcme1yj
form: example
title: Refresh token reuse
subjects:
  - BR-01m4v5j7btkhbb5ah08j7f5h4y
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Refresh with rotation /
      Scenario: Refresh token reuse
    - "local/nitro/tests/oauth/tokens_test.php · test_refresh_reuse_revokes_everything"
  quote: an already used refresh token is presented again
---
# Refresh token reuse

## Given

A refresh token has already been exchanged once for a new token pair.

## When

an already used refresh token is presented again

## Then

the response is `invalid_grant` and all tokens of that grant are revoked
