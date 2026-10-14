---
id: EX-01m4v5j7btpshmgq2s59qw3gfm
form: example
title: Unknown client
subjects:
  - BR-01m4v5j7btm6qyqm6d798d1zpa
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Admin client
      registration / Scenario: Unknown client
    - "local/nitro/tests/behat/authorize.feature · An unknown client is refused without redirecting"
  quote: >-
    an authorization request names a client ID that is neither registered nor an
    allowed CIMD URL
---
# Unknown client

## Given

nitro is enabled and the user is logged in to Moodle.

## When

an authorization request names a client ID that is neither registered nor an allowed CIMD URL

## Then

the request is rejected with an error page and no redirect happens
