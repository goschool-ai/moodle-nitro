---
id: EX-01m4v5zbgps1nwhyjp0g590ag8
form: example
title: New quiz is hidden
subjects:
  - BR-01m4v5za4v8sq9emc17gc9a180
capability: quiz-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/quiz_tools_test.php · test_quiz_opens_only_once_it_has_questions"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# New quiz is hidden

## Given

A Moodle 5.0 or later site; a teacher who holds `local/nitro:use` and may add quizzes in the course.

## When

the teacher creates a quiz without saying whether it is visible

## Then

the quiz exists hidden from students and the result says it is not visible
