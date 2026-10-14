---
id: EX-01m4v5j7bt20y58y0n7q2y9hr2
form: example
title: Message sent
subjects:
  - BR-01m4v5j7bt5v2zqqzj16kcknm0
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/audit-log/spec.md · Requirement: No content in the log /
      Scenario: Message sent
    - "openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for writes that reach students"
  quote: '`message_students` sends a message'
---
# Message sent

## Given

A teacher approved a `message_students` preview for some students and the client confirmed it with the token.

## When

`message_students` sends a message

## Then

the event records the recipient count and the recipient user IDs, not the message text
