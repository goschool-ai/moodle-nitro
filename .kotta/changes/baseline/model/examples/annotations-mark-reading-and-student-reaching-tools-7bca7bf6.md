---
id: EX-01m4v619wtg0fe8s7h7bca7bf6
form: example
title: Annotations mark reading and student-reaching tools
subjects:
  - BR-01m4v61205akg1k1wb0btt3wym
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/mcp/server_test.php · test_tools_list_matches_snapshot"
    - "local/nitro/tests/fixtures/tools_list.json"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Annotations mark reading and student-reaching tools

## Given

An authenticated client on protocol version 2025-06-18, with every tool allowed.

## When

it calls `tools/list`

## Then

`list_courses` carries `title` "List my courses", `readOnlyHint: true` and `destructiveHint: false`; `message_students` carries `destructiveHint: true`; `save_page` is neither read-only nor destructive
