---
id: EX-01m4v5j7btp8fjehvf6xj4ptmr
form: example
title: No address configured
subjects:
  - BR-01m4v5j7bt8fgb45bv3ex3br5x
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro /
      Scenario: No address configured
    - 'local/nitro/tests/external/send_feedback_test.php · test_no_address_configured'
  quote: the site has no valid feedback address
  inferred: 'Given supplied from the test setup (empty address).'
---
# No address configured

## Given

Feedback is switched on, but the feedback address is empty or not a valid email address.

## When

the site has no valid feedback address

## Then

the call returns an error saying so, instead of pretending to send
