---
id: EX-01m4v5j7btb6kqq3ykj0jdcmya
form: example
title: Explicit identity request
subjects:
  - BR-01m4v5j7bteex1ptp70awxzkmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Data minimisation /
      Scenario: Explicit identity request
    - "local/nitro/tests/external/list_participants_test.php · test_explicit_identity_request"
    - "local/nitro/classes/external/list_participants.php · include_identity"
  quote: >-
    a teacher asks for email addresses and has permission to view them in the
    participants page
  inferred: "Given: that the permission is the site's identity-field setting, read from the test and list_participants.php."
---
# Explicit identity request

## Given

The site shows email address among the user identity fields, and the teacher may view participants and user identity in the course.

## When

a teacher asks for email addresses and has permission to view them in the participants page

## Then

the result includes email addresses
