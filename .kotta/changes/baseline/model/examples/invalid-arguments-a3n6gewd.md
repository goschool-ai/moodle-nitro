---
id: EX-01m4v5j7bt9568sfr7a3n6gewd
form: example
title: Invalid arguments
subjects:
  - BR-01m4v5j7bt31c41e3fpbthvp6m
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Tool invocation /
      Scenario: Invalid arguments
    - "local/nitro/tests/mcp/dispatcher_test.php · test_invalid_arguments_name_the_parameter"
  quote: a client calls a tool with missing or wrongly typed arguments
  inferred: "The concrete case, read from dispatcher_test."
---
# Invalid arguments

## Given

An authenticated client calls an allowlisted tool, for example `list_courses`, with an argument its parameter definition does not accept or without a required one.

## When

a client calls a tool with missing or wrongly typed arguments

## Then

the result has `isError: true` and names the offending parameter, and nothing is changed
