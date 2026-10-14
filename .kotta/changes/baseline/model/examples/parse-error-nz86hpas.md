---
id: EX-01m4v5j7bta6t3s3vpnz86hpas
form: example
title: Parse error
subjects:
  - BR-01m4v5j7bt1c9afnvsv8z4wpb4
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Import questions /
      Scenario: Parse error
    - "local/nitro/tests/external/quiz_tools_test.php · test_parse_error_imports_nothing"
  quote: the input contains a question the importer cannot parse
  inferred: "Given taken from the setup of quiz_tools_test.php::test_parse_error_imports_nothing."
---
# Parse error

## Given

A Moodle 5.0 or later site; a teacher holding `local/nitro:use` and `moodle/question:add`; the GIFT input holds valid questions and one with a missing closing brace.

## When

the input contains a question the importer cannot parse

## Then

no question is imported and the result reports the error with the position of the faulty question
