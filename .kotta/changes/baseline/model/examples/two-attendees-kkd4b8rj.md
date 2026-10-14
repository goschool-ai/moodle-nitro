---
id: EX-01m4v5j7btax15kvsykkd4b8rj
form: example
title: Two attendees
subjects:
  - BR-01m4v5j7bty7qzadb40790epke
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Personal demo
      course on signup / Scenario: Two attendees
    - 'local/nitrosandbox/tests/provisioner_test.php · test_two_attendees_are_isolated'
  quote: two teachers sign up
  inferred: 'Given supplied from the test setup.'
---
# Two attendees

## Given

On the sandbox, onboarding is enabled and the template course backup is uploaded.

## When

two teachers sign up

## Then

each has their own course, and neither is enrolled in or can see the other's course
