---
id: EX-01m4v5j7btnpgy3w4ahpff0hnx
form: example
title: Denied call
subjects:
  - BR-01m4v5j7btrxsgv3r22s4jrnnz
  - UC-01m4v5t00xh3nwy7f83gkbmrr8
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/audit-log/spec.md · Requirement: Event for every tool call
      / Scenario: Denied call
    - "local/nitro/tests/mcp/dispatcher_test.php · test_every_call_is_logged"
  quote: a call fails on a capability check
  inferred: "Given: read from the dispatcher test."
---
# Denied call

## Given

A teacher's client is connected, and the teacher lacks a capability the tool checks in the target course, for example `local/nitro:use` there.

## When

a call fails on a capability check

## Then

a tool-call event with outcome `error` is still recorded
