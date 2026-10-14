---
id: EX-01m4v5j7bt9r1wf6t3am8wdmy2
form: example
title: Messaging a fictitious student
subjects:
  - BR-01m4v5j7bt0e86kt0sss4wfhmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Isolated
      fictitious students / Scenario: Messaging a fictitious student
    - 'local/nitrosandbox/tests/provisioner_test.php · test_fictitious_student_cannot_log_in_and_gets_no_email'
  quote: a teacher sends a message to fictitious students through `message_students`
  inferred: 'Given supplied from the provisioner test.'
---
# Messaging a fictitious student

## Given

A demo course has been provisioned on the sandbox, and its fictitious students have the `nologin` authentication method and addresses on `example.invalid`.

## When

a teacher sends a message to fictitious students through `message_students`

## Then

the message is stored in Moodle and no email leaves the sandbox
