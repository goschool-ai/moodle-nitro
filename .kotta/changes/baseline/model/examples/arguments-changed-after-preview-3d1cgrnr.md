---
id: EX-01m4v5j7bt5v7mm6xr3d1cgrnr
form: example
title: Arguments changed after preview
subjects:
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students / Scenario: Arguments changed after preview
    - "openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for writes that reach students"
  quote: the confirming call's arguments differ from the previewed ones
---
# Arguments changed after preview

## Given

A teacher called a confirmation tool without a token and received a preview and a confirmation token that is still valid.

## When

the confirming call's arguments differ from the previewed ones

## Then

nothing is executed and the result asks for a new preview
