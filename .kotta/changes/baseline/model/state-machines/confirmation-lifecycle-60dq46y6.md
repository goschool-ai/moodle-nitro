---
id: SM-01m4v5t3hf8q8fzbdz60dq46y6
form: state-machine
title: "Confirmation lifecycle"
entity:
  - E-01m4v5t1x7er5z3tw4pn786gat
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5"
    - "openspec/specs/write-confirmation/spec.md"
  quote: "preview first, execute only with a confirmation token"
  inferred: null
---
# Confirmation lifecycle

## Governed lifecycle

A write that reaches students, from preview to execution.

## States

- **pending**: preview shown, token issued
- **used**: the confirmed call ran
- **expired**: 10 minutes passed

## Transitions

- (first call) → pending: the tool returns a preview and a token, nothing is sent
- pending → used: the call is repeated with the same arguments and the token
- pending → pending (a new confirmation): the teacher asks for a change, a new preview is made
- pending → expired: after 10 minutes; a repeated call is refused
