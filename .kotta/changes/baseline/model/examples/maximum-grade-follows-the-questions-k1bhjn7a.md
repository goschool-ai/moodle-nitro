---
id: EX-01m4v5j7bte92nqt59k1bhjn7a
form: example
title: Maximum grade follows the questions
subjects:
  - BR-01m4v5j7btcd04kypy9wjaecwh
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz / Scenario: Maximum grade follows the questions
    - "local/nitro/tests/external/quiz_tools_test.php · test_fixed_questions, test_max_grade_the_teacher_asked_for"
  quote: questions worth 8 marks together are added to a quiz
  inferred: "Given taken from the setup of quiz_tools_test.php::test_fixed_questions, test_max_grade_the_teacher_asked_for."
---
# Maximum grade follows the questions

## Given

A quiz without student attempts, created with `create_quiz` and so carrying the site's default maximum grade; the questions to add are in a bank the teacher may use.

## When

questions worth 8 marks together are added to a quiz

## Then

the quiz's maximum grade becomes 8, so Moodle does not scale the marks, unless the teacher gives a maximum of their own
