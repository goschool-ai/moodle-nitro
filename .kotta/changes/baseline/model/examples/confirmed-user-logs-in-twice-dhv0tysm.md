---
id: EX-01m4v5xjssyqk484e0dhv0tysm
form: example
title: Confirmed user logs in twice
capability: sandbox-onboarding
subjects:
  - BR-01m4v5xh3edmajdb0n0mwp9dxj
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitrosandbox/tests/provisioner_test.php · test_observer_queues_once_for_confirmed_users'
  inferred: 'Read from the test.'
---
# Confirmed user logs in twice

## Given

Onboarding is on, and a confirmed and an unconfirmed account were created after it started.

## When

both log in, and the confirmed one logs in again before cron has run

## Then

exactly one demo course task is queued, for the confirmed account
