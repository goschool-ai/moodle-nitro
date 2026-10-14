---
id: EX-01m4v5j7btwmw3c7wtnykxzd6x
form: example
title: Default participant list
subjects:
  - BR-01m4v5j7bteex1ptp70awxzkmt
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Data minimisation /
      Scenario: Default participant list
    - "local/nitro/tests/external/list_participants_test.php · test_default_list_has_no_identity_fields"
  quote: a teacher calls `list_participants` without asking for identity fields
  inferred: "Given: read from the participants test."
---
# Default participant list

## Given

A teacher holds `local/nitro:use` and `moodle/course:viewparticipants` in a course whose students have email addresses and ID numbers.

## When

a teacher calls `list_participants` without asking for identity fields

## Then

the result contains user ID, full name, roles and groups, and no email address or ID number
