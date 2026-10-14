---
id: EX-01m4v5j7btet673ckes9cjm446
form: example
title: No shared bank yet
subjects:
  - BR-01m4v5j7bt1c9afnvsv8z4wpb4
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Import questions /
      Scenario: No shared bank yet
    - "local/nitro/tests/external/quiz_tools_test.php · test_gift_into_new_shared_bank"
  quote: the course has no shared question bank
  inferred: "Given taken from the setup of quiz_tools_test.php::test_gift_into_new_shared_bank."
---
# No shared bank yet

## Given

A Moodle 5.0 or later course with no Question bank activity; the teacher holds `local/nitro:use` and `moodle/question:add`.

## When

the course has no shared question bank

## Then

one is created in the course before importing
