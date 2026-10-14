---
id: EX-01m4v5j7bts7kdatr1edf8sgs1
form: example
title: Tool call makes no outbound request
subjects:
  - BR-01m4v5j7bt63kng1skzff30aq7
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: No third-party data
      flow / Scenario: Tool call makes no outbound request
    - "openspec/specs/access-control/spec.md · Requirement: No third-party data flow"
  quote: any tool is called
---
# Tool call makes no outbound request

## Given

nitro is switched on, and a teacher's client is connected.

## When

any tool is called

## Then

the plugin makes no outbound network request
