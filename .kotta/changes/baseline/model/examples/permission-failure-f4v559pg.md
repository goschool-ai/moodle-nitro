---
id: EX-01m4v5j7btn8knx7hmf4v559pg
form: example
title: Permission failure
subjects:
  - BR-01m4v5j7bt31c41e3fpbthvp6m
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Tool invocation /
      Scenario: Permission failure
    - "local/nitro/tests/mcp/dispatcher_test.php · test_gate_granted_in_one_course_only"
  quote: a tool fails because the user lacks a required Moodle capability
  inferred: "The concrete case, read from dispatcher_test."
---
# Permission failure

## Given

An authenticated user calls a tool in a context where they lack a Moodle capability it requires, for example a teacher who holds `local/nitro:use` only in course A calls a tool on course B.

## When

a tool fails because the user lacks a required Moodle capability

## Then

the result has `isError: true` and names the missing capability
