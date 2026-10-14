---
id: EX-01m4v5j7btfndrnnh8kzk28f7s
form: example
title: Successful call
subjects:
  - BR-01m4v5j7bt31c41e3fpbthvp6m
  - UC-01m4v5t0c3b5wt6eggggfwqy05
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Tool invocation /
      Scenario: Successful call
    - "local/nitro/tests/mcp/dispatcher_test.php · test_successful_call_runs_as_user"
  quote: a client calls an allowlisted tool with valid arguments
---
# Successful call

## Given

An authenticated client's user holds the capabilities the tool needs in the target context.

## When

a client calls an allowlisted tool with valid arguments

## Then

the result contains the tool's output as JSON text content and `isError` is false
