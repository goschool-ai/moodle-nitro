---
id: EX-01m4v5xk06tahmjz23fgdyz96j
form: example
title: Account older than onboarding
capability: sandbox-onboarding
subjects:
  - BR-01m4v5xh3edmajdb0n0mwp9dxj
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitrosandbox/tests/provisioner_test.php · test_users_created_before_onboarding_get_nothing'
  inferred: 'Read from the test.'
---
# Account older than onboarding

## Given

Onboarding was switched on just now, and a confirmed account was created a week earlier.

## When

that account logs in

## Then

no demo course is queued and the account is not marked
