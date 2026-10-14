---
id: EX-01m4v5j7bt47wzpbzcp073nzkk
form: example
title: Quiz already attempted
subjects:
  - BR-01m4v5j7btcd04kypy9wjaecwh
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz / Scenario: Quiz already attempted
    - "local/nitro/tests/external/quiz_tools_test.php · test_quiz_already_attempted"
  quote: the quiz already has student attempts
  inferred: "Given taken from the setup of quiz_tools_test.php::test_quiz_already_attempted."
---
# Quiz already attempted

## Given

A quiz with questions on which a student has started an attempt (the teacher's own previews do not count).

## When

the quiz already has student attempts

## Then

nothing is added and the error explains that the quiz structure is locked, as in the web UI
