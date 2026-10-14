---
id: EX-01m4v5j7bt1yeh731gem976q93
form: example
title: Students do not overlap
subjects:
  - BR-01m4v5j7bt0e86kt0sss4wfhmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Isolated
      fictitious students / Scenario: Students do not overlap
    - 'local/nitrosandbox/tests/provisioner_test.php · test_two_attendees_are_isolated'
  quote: a teacher lists the participants of their demo course
  inferred: 'Given supplied from the test setup.'
---
# Students do not overlap

## Given

Two attendees have signed up, and each has received a demo course.

## When

a teacher lists the participants of their demo course

## Then

none of the students is enrolled in any other attendee's course, so no student profile reveals another attendee's course
