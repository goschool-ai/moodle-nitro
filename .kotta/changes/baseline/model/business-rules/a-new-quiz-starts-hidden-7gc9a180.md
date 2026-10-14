---
id: BR-01m4v5za4v8sq9emc17gc9a180
form: business-rule
title: A new quiz starts hidden
capability: quiz-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 11.10"
    - "openspec/specs/quiz-authoring/spec.md · Scenario: Students never see an empty quiz"
    - "local/nitro/classes/external/create_quiz.php · execute_parameters"
    - "local/nitro/tests/external/quiz_tools_test.php · test_practice_quiz_until_friday, test_quiz_opens_only_once_it_has_questions"
  inferred: "The default is read from create_quiz::execute_parameters; the reason is stated in that parameter's description, the scenario and tasks 11.10."
---
# A new quiz starts hidden

## Rule

`create_quiz` SHALL create the quiz hidden from students unless the call sets `visible`, and SHALL echo its visibility in the result.

## Rationale

Students must never open an empty quiz: the quiz stays hidden until `add_questions_to_quiz` makes it visible together with its questions, a fix from the first full demo run.

## Scope

Applies to `create_quiz`. Making the quiz visible afterwards goes through `make_visible` of `add_questions_to_quiz`, which also needs `moodle/course:activityvisibility`.
