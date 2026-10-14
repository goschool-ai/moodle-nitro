---
id: EX-01m4v5j7btz8xv1gqe8wsvn5wg
form: example
title: Admin disables the plugin
subjects:
  - BR-01m4v5j7bt0mhsaby7fyp9a02d
  - UC-01m4v5sz8xw7h30c3nba92b51n
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Site kill switch /
      Scenario: Admin disables the plugin
    - "local/nitro/tests/mcp/server_test.php · test_inactive_plugin"
    - "local/nitro/tests/oauth/tokens_test.php · test_plugin_disabled"
  quote: the admin turns the setting off
  inferred: "Given: read from the server and token tests."
---
# Admin disables the plugin

## Given

nitro is switched on, and a teacher's AI client holds a valid access token.

## When

the admin turns the setting off

## Then

every MCP request is answered with HTTP 503 and every OAuth request is refused, until the setting is turned on again
