---
id: SM-01m4v5t4e49wwfhqrsc35b2jxb
form: state-machine
title: "Feedback delivery"
entity:
  - E-01m4v5t32wgvnbzb0kqnrnbe6m
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 9. Troubleshooting"
    - "openspec/specs/feedback/spec.md"
  quote: "If the mail server was down, nitro queues the message and retries, so a single failure is not lost."
  inferred: "States named from the admin guide and the feedback spec."
---
# Feedback delivery

## Governed lifecycle

A feedback mail, from the teacher's approval to delivery.

## States

- **previewed**
- **sent**
- **queued for retry**

## Transitions

- (first call) → previewed
- previewed → sent: the teacher approved and the mail server accepted it
- previewed → queued for retry: the mail server was unreachable
- queued for retry → sent: a later cron run delivers it
