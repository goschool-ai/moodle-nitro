---
id: EX-01m4v5j7bthep8hwqdafmx7xzp
form: example
title: Not enough questions
subjects:
  - BR-01m4v5j7btcd04kypy9wjaecwh
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz / Scenario: Not enough questions
    - "local/nitro/tests/external/quiz_tools_test.php · test_not_enough_questions"
  quote: a teacher asks for more random questions than the category holds
  inferred: "Given taken from the setup of quiz_tools_test.php::test_not_enough_questions."
---
# Not enough questions

## Given

Category "Week 4" of the course's shared bank holds 15 ready questions, and a quiz without student attempts exists.

## When

a teacher asks for more random questions than the category holds

## Then

nothing is added and the error states how many questions the category holds
