---
id: BR-01m4v5x24z56j9rgcvpv0pes77
form: business-rule
title: Feedback that cannot be mailed is retried
capability: feedback
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/classes/external/send_feedback.php · execute()'
    - 'local/nitro/classes/task/send_feedback_mail.php · send_feedback_mail'
    - 'local/nitro/tests/external/send_feedback_test.php · test_unreachable_mail_server_queues_a_retry'
    - 'local/nitro/README.md · 1. Requirements; Troubleshooting (Feedback is not delivered)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 9.4'
  inferred: 'The rule is read from the code and test; the feedback spec states it only as a scenario.'
---
# Feedback that cannot be mailed is retried

## Rule

When the mail server does not take an approved feedback message, `send_feedback` SHALL report the message as queued, not sent, and SHALL keep it in a Moodle ad hoc task that retries the mail until it goes out. An approved feedback message MUST NOT be silently lost.

## Rationale

A teacher's report of what is missing or broken is worth keeping even when the mail server is briefly unreachable; the README promises that a single failure is not lost. A failed ad hoc task is rescheduled by Moodle with a growing delay, so an outage costs time, not the report.

## Scope

Feedback messages the teacher has already approved through `send_feedback`. The retry needs cron to run (README, Requirements). It does not cover a site with no valid feedback address, which is refused before any preview.
