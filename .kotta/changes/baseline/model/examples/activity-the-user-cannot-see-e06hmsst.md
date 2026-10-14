---
id: EX-01m4v5j7btmqjnxszje06hmsst
form: example
title: Activity the user cannot see
subjects:
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read an activity /
      Scenario: Activity the user cannot see
    - "local/nitro/tests/external/read_activity_test.php · test_requires_the_gate"
  quote: a teacher without access to the course calls `read_activity`
  inferred: "Given taken from the setup of read_activity_test.php::test_requires_the_gate."
---
# Activity the user cannot see

## Given

An activity exists in a course in which the calling teacher does not hold `local/nitro:use`.

## When

a teacher without access to the course calls `read_activity`

## Then

the call is refused
