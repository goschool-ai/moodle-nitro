---
id: EX-01m4v5j7btdzqvzdjd4rz7emq7
form: example
title: Read-only site
subjects:
  - BR-01m4v5j7bt41bfj98fr44h8jj9
  - UC-01m4v5swm98nywm4eb9ez2hth6
  - UC-01m4v5szjk7rmysaj066c8mz69
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Per-tool admin
      control / Scenario: Read-only site
    - "local/nitro/tests/mcp/server_test.php · test_read_only_site"
  quote: an admin unticks every write tool
  inferred: "Given: read from the server test."
---
# Read-only site

## Given

Every tool is allowed, and a teacher's client is connected.

## When

an admin unticks every write tool

## Then

only read tools are offered, and no tool can change a course or reach a student
