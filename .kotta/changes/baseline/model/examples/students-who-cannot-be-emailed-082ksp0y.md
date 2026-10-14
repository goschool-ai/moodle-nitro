---
id: EX-01m4v5j7btv35xbtqa082ksp0y
form: example
title: Students who cannot be emailed
subjects:
  - BR-01m4v5j7btbvevrsab4mp6a1py
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Post announcement /
      Scenario: Students who cannot be emailed
    - 'local/nitro/tests/external/messaging_test.php · test_announcement_says_how_many_get_mail'
  quote: >-
    fewer people will be emailed than can read the course, because their
    accounts cannot sign in, are suspended or were never confirmed
  inferred: 'Given supplied from the test setup (one student with nologin).'
---
# Students who cannot be emailed

## Given

Some enrolled users cannot receive mail because their accounts cannot sign in (such as the sandbox's fictitious students), are suspended or were never confirmed.

## When

fewer people will be emailed than can read the course, because their accounts cannot sign in, are suspended or were never confirmed

## Then

the preview says how many of how many get mail and why, and that everyone still sees the announcement in the course
