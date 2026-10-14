---
id: EX-01m4v5j7btddxtnr3hwjatkdda
form: example
title: Missing permission
subjects:
  - BR-01m4v5j7bt1c9afnvsv8z4wpb4
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Import questions /
      Scenario: Missing permission
    - "local/nitro/tests/external/quiz_tools_test.php · test_missing_permission"
  quote: 'the user lacks `moodle/question:add` in the target bank'
  inferred: "Given taken from the setup of quiz_tools_test.php::test_missing_permission."
---
# Missing permission

## Given

A teacher holds `local/nitro:use` in the course, but `moodle/question:add` is prohibited for them there.

## When

the user lacks `moodle/question:add` in the target bank

## Then

nothing is imported and the error names the capability
