---
id: EX-01m4v5j7bt89kx58vwd1q1twzy
form: example
title: Confirmed call executes
subjects:
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students / Scenario: Confirmed call executes
    - "openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for writes that reach students"
    - "local/nitro/tests/local/confirmation_test.php · test_confirmed_call"
  quote: >-
    the same user calls the tool again with identical arguments and the returned
    token within its validity
---
# Confirmed call executes

## Given

A teacher called a confirmation tool without a token, received a preview and a confirmation token, and approved the preview.

## When

the same user calls the tool again with identical arguments and the returned token within its validity

## Then

the action is executed and the result reports what was done
