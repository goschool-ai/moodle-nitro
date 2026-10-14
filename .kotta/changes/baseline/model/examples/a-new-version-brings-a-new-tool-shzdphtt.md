---
id: EX-01m4v5j7btx8z2qxknshzdphtt
form: example
title: A new version brings a new tool
subjects:
  - BR-01m4v5j7btv9nytfppj0ebv4mt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/mcp-endpoint/spec.md · Requirement: Allowlisted tool
      discovery / Scenario: A new version brings a new tool
    - "local/nitro/db/upgrade.php · local_nitro_allow_new_tools"
  quote: a plugin upgrade adds a tool
  inferred: "The Given, read from db/upgrade.php."
---
# A new version brings a new tool

## Given

A site upgraded from an earlier nitro version, whose stored tool allowlist was saved before the new tool existed.

## When

a plugin upgrade adds a tool

## Then

the upgrade adds it to the allowlist, so it is listed without the admin having to find it. For the demo the upgrade adds every tool missing from the allowlist, so a tool the admin removed earlier comes back and has to be removed again; remembering removals is left to the pilot
