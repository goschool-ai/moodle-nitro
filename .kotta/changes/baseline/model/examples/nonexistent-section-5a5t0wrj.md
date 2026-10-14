---
id: EX-01m4v5j7btc2pj5hvx5a5t0wrj
form: example
title: Nonexistent section
subjects:
  - BR-01m4v5j7bt9rfe2s8t3a1jgyv3
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Save page /
      Scenario: Nonexistent section
    - "local/nitro/tests/external/save_page_test.php · test_nonexistent_section"
  quote: the given section number does not exist in the course
  inferred: "Given taken from the setup of save_page_test.php::test_nonexistent_section."
---
# Nonexistent section

## Given

A teacher who holds `local/nitro:use` and may manage activities in a course whose sections are numbered up to a highest number.

## When

the given section number does not exist in the course

## Then

nothing is saved and the error names the highest existing section number
