---
id: EX-01m4v5j7btb2g3rqktnp3kt90m
form: example
title: Random questions from a category
subjects:
  - BR-01m4v5j7btcd04kypy9wjaecwh
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz / Scenario: Random questions from a category
    - "local/nitro/tests/external/quiz_tools_test.php · test_random_questions_from_a_category"
  quote: >-
    a teacher adds 10 random questions from category "Week 4" that holds 15
    questions
  inferred: "Given taken from the setup of quiz_tools_test.php::test_random_questions_from_a_category."
---
# Random questions from a category

## Given

Category "Week 4" of the course's shared bank holds 15 ready questions, and an empty quiz without attempts exists.

## When

a teacher adds 10 random questions from category "Week 4" that holds 15 questions

## Then

the quiz has 10 random slots drawing from that category and its maximum grade reflects the marks
