---
id: EX-01m4v5j7btpk7kje69dk4f44js
form: example
title: Switched off
subjects:
  - BR-01m4v5j7bt8fgb45bv3ex3br5x
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro /
      Scenario: Switched off
    - 'local/nitro/tests/external/send_feedback_test.php · test_switched_off'
    - 'local/nitro/classes/local/tools.php · allowed()'
  quote: the admin switches feedback off
  inferred: 'Given supplied from the test setup.'
---
# Switched off

## Given

nitro is switched on, and a teacher's AI client is connected.

## When

the admin switches feedback off

## Then

the tool is not listed and calling it returns an error
