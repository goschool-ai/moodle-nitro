---
id: EX-01m4v5j7bte8bd3z1scavmxqpk
form: example
title: Activity with no text
subjects:
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read an activity /
      Scenario: Activity with no text
    - "local/nitro/tests/external/read_activity_test.php · test_activity_without_readable_text"
  quote: the module is an uploaded file with no description
  inferred: "Given taken from the setup of read_activity_test.php::test_activity_without_readable_text."
---
# Activity with no text

## Given

A teacher holding `local/nitro:use` in a course that has a File activity (an uploaded file) with an empty description.

## When

the module is an uploaded file with no description

## Then

the result carries a note that the activity keeps no readable text, instead of an empty answer
