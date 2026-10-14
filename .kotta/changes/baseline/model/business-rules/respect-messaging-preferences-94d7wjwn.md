---
id: BR-01m4v5j7btr5a1bdhf94d7wjwn
form: business-rule
title: Respect messaging preferences
capability: messaging
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Respect messaging
      preferences
    - 'PRODUCT.md · Product Principles 1; Users (students)'
    - 'openspec/specs/messaging/spec.md · Purpose'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 9.1'
    - 'local/nitro/classes/external/message_students.php · undeliverable()'
  quote: >-
    Delivery SHALL go through Moodle's messaging system, so recipients'
    messaging privacy settings and notification preferences apply as for a
    message sent from the web UI.
  inferred: 'Why the undelivered recipients are reported (so the teacher can reach them another way) is read from the tool''s result fields; no source states it.'
---
# Respect messaging preferences

## Rule

Delivery SHALL go through Moodle's messaging system, so recipients' messaging privacy settings and notification preferences apply as for a message sent from the web UI. Recipients who cannot be messaged SHALL be reported, not silently skipped.

## Rationale

Through the AI exactly as much is possible as in the web UI, no more (principle 1), and students are reached only through Moodle's own channels (PRODUCT.md, Users), so their messaging privacy and notification choices must hold. Reporting who could not be messaged tells the teacher which students did not get the reminder and need another way.

## Scope

Delivery by `message_students`. The preview already lists the recipients who cannot be reached (suspended account, or messaging settings that do not accept the teacher), and the result lists who got the message and who did not, with the reason. Notifications of announcements are not covered; Moodle's forum handles those.
