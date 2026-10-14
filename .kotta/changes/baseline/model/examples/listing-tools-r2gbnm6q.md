---
id: EX-01m4v5j7btndzp1ntmr2gbnm6q
form: example
title: Listing tools
subjects:
  - BR-01m4v5j7btv9nytfppj0ebv4mt
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Allowlisted tool
      discovery / Scenario: Listing tools
  quote: an authenticated client calls `tools/list`
---
# Listing tools

## Given

nitro is enabled, the client holds a valid token, and the admin has ticked some of the tools in Allowed tools.

## When

an authenticated client calls `tools/list`

## Then

every allowlisted tool is returned with `name`, `description` and `inputSchema`, and no tool outside the allowlist is returned
