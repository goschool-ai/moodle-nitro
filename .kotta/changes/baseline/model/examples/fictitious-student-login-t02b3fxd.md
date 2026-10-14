---
id: EX-01m4v5j7bte89q48vtt02b3fxd
form: example
title: Fictitious student login
subjects:
  - BR-01m4v5j7bt0e86kt0sss4wfhmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Isolated
      fictitious students / Scenario: Fictitious student login
    - 'local/nitrosandbox/tests/provisioner_test.php · test_fictitious_student_cannot_log_in_and_gets_no_email'
    - 'local/nitrosandbox/classes/provisioner.php · build()'
  quote: someone tries to log in as a fictitious student
  inferred: 'Given supplied from the provisioner and its test (nologin accounts without a usable password).'
---
# Fictitious student login

## Given

A demo course has been provisioned, and its fictitious students have the `nologin` authentication method and no usable password.

## When

someone tries to log in as a fictitious student

## Then

the login is refused
