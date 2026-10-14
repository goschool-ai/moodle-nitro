---
id: EX-01m4v5j7bt8ak3mbkdkpr3pkta
form: example
title: Submissions to work with
subjects:
  - BR-01m4v5j7bt0e86kt0sss4wfhmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Isolated
      fictitious students / Scenario: Submissions to work with
    - 'local/nitrosandbox/tests/provisioner_test.php · test_fictitious_students_and_submissions'
    - 'local/nitrosandbox/classes/provisioner.php · seed_assignment()'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9 implementation notes'
  quote: >-
    a teacher calls `list_submissions` with filter `missing` on the demo
    assignment after its due date
  inferred: 'Given supplied from the provisioner, its test and the D9 notes.'
---
# Submissions to work with

## Given

A demo course has just been provisioned. Its assignment's due date has just passed, the cut-off is three days ahead, and 4 students submitted nothing, 4 submitted late and 12 submitted on time.

## When

a teacher calls `list_submissions` with filter `missing` on the demo assignment after its due date

## Then

a non-empty list is returned, and filter `late` also returns students
