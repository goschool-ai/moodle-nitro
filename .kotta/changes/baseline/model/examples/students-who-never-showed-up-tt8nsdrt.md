---
id: EX-01m4v5j7btj4nzg920tt8nsdrt
form: example
title: Students who never showed up
subjects:
  - BR-01m4v5j7btfttg85w3vrm5t4br
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List participants /
      Scenario: Students who never showed up
    - "local/nitro/tests/external/list_participants_test.php · test_never_accessed"
  quote: a teacher asks for students who never accessed the course
  inferred: "Given taken from the setup of list_participants_test.php::test_never_accessed."
---
# Students who never showed up

## Given

A course where some enrolled students have opened the course and others never have.

## When

a teacher asks for students who never accessed the course

## Then

only students with no course access are returned
