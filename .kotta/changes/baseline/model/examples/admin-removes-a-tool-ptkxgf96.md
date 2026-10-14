---
id: EX-01m4v5j7bt8p0cgbtmptkxgf96
form: example
title: Admin removes a tool
subjects:
  - BR-01m4v5j7btv9nytfppj0ebv4mt
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Allowlisted tool
      discovery / Scenario: Admin removes a tool
    - "local/nitro/tests/mcp/server_test.php · test_admin_removes_a_tool"
  quote: a site admin removes a tool from the allowlist
---
# Admin removes a tool

## Given

nitro is enabled, a client is connected with a valid token, and the tool is on the site's allowlist.

## When

a site admin removes a tool from the allowlist

## Then

the tool disappears from `tools/list` and calling it returns an error
