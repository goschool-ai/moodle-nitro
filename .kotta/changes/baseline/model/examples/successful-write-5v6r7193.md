---
id: EX-01m4v5j7btadgaevsj5v6r7193
form: example
title: Successful write
subjects:
  - BR-01m4v5j7btrxsgv3r22s4jrnnz
  - UC-01m4v5t00xh3nwy7f83gkbmrr8
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/audit-log/spec.md · Requirement: Event for every tool call
      / Scenario: Successful write
    - "local/nitro/tests/external/save_page_test.php · test_first_publish"
    - "local/nitro/tests/mcp/dispatcher_test.php · test_every_call_is_logged"
  quote: a teacher creates a page through `save_page`
  inferred: "Given: read from the save_page and dispatcher tests."
---
# Successful write

## Given

A teacher holds `local/nitro:use` and `moodle/course:manageactivities` in the course, their client is connected, and no page with the given key exists yet.

## When

a teacher creates a page through `save_page`

## Then

the course log shows a nitro tool-call event naming the teacher, the client, `save_page` and the course, followed by Moodle's own module-created event
