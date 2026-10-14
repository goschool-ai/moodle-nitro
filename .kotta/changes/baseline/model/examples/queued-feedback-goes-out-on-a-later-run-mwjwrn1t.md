---
id: EX-01m4v5xhpvgvn6ebrymwjwrn1t
form: example
title: Queued feedback goes out on a later run
capability: feedback
subjects:
  - BR-01m4v5x24z56j9rgcvpv0pes77
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/tests/external/send_feedback_test.php · test_unreachable_mail_server_queues_a_retry'
  inferred: 'Read from the test.'
---
# Queued feedback goes out on a later run

## Given

The teacher approved a feedback message, and the mail server could not take it, so the result said it was queued.

## When

cron runs the queued feedback task and the mail server is reachable again

## Then

the message arrives at the feedback address with the approved text and the subject "nitro feedback"
