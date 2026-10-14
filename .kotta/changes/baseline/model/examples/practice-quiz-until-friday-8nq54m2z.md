---
id: EX-01m4v5j7btwv8tjj3g8nq54m2z
form: example
title: Practice quiz until Friday
subjects:
  - BR-01m4v5j7btg9px95xnpj3jzeyy
  - UC-01m4v5sydbccy34s28xmqtfd5k
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Create quiz /
      Scenario: Practice quiz until Friday
    - "local/nitro/tests/external/quiz_tools_test.php · test_practice_quiz_until_friday"
  quote: >-
    a teacher creates a quiz with close time Friday 23:59, unlimited attempts
    and preset `practice`
  inferred: "Given taken from the setup of quiz_tools_test.php::test_practice_quiz_until_friday."
---
# Practice quiz until Friday

## Given

A Moodle 5.0 or later site; a teacher who may add quizzes in the course, with the time zone Europe/Budapest.

## When

a teacher creates a quiz with close time Friday 23:59, unlimited attempts and preset `practice`

## Then

the quiz exists with those settings and shows answers after each attempt
