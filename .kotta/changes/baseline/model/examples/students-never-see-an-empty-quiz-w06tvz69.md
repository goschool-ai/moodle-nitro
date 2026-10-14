---
id: EX-01m4v5j7bt053hqpxrw06tvz69
form: example
title: Students never see an empty quiz
subjects:
  - BR-01m4v5j7btcd04kypy9wjaecwh
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz / Scenario: Students never see an empty quiz
    - "local/nitro/tests/external/quiz_tools_test.php · test_quiz_opens_only_once_it_has_questions"
  quote: a teacher wants a new quiz open straight away
  inferred: "Given taken from the setup of quiz_tools_test.php::test_quiz_opens_only_once_it_has_questions."
---
# Students never see an empty quiz

## Given

Questions are already imported into the course's bank; the teacher may add and manage quizzes and change activity visibility.

## When

a teacher wants a new quiz open straight away

## Then

the quiz is created hidden and becomes visible in the same call that adds its questions
