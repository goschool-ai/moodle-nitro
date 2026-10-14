---
id: EX-01m4v5j7btk3946cw6h4w4c68e
form: example
title: Teacher signs up
subjects:
  - BR-01m4v5j7bty7qzadb40790epke
  - UC-01m4v5swcbnv1j08a3n056jh08
  - UC-01m4v5sz31mn30kpk5nqksd170
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Personal demo
      course on signup / Scenario: Teacher signs up
    - 'local/nitrosandbox/tests/provisioner_test.php · test_template_restores_cleanly_into_a_demo_course'
    - 'deploy/sandbox.sh · --first'
    - 'deploy/email.sh'
  quote: a teacher confirms their sandbox account
  inferred: 'Given supplied from the sandbox deployment scripts and the provisioner test.'
---
# Teacher signs up

## Given

On the sandbox, onboarding is enabled, the template course backup is uploaded and email self-registration is on, and a teacher has just created an account.

## When

a teacher confirms their sandbox account

## Then

within one minute they are the editing teacher of a new course "Demo NN" containing the week-4 material, an assignment and an empty course question bank, and they can connect an AI client and call `list_courses` to see it
