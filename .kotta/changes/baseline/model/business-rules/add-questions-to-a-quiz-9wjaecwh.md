---
id: BR-01m4v5j7btcd04kypy9wjaecwh
form: business-rule
title: Add questions to a quiz
capability: quiz-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Add questions to a
      quiz
    - "openspec/specs/quiz-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 11.9, 11.10"
    - "PRODUCT.md · Product Principles"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D7. Questions through Moodle's importers"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals"
    - "local/nitro/classes/external/add_questions_to_quiz.php · execute"
    - "local/nitro/tests/external/quiz_tools_test.php · test_fixed_questions, test_quiz_opens_only_once_it_has_questions, test_quiz_already_attempted"
  quote: >-
    `add_questions_to_quiz` SHALL add specific questions by ID, or a number of
    random questions drawn from a category, to a quiz, with a mark per question
    and a number of questions
  inferred: "Scope details (which banks random questions come from, teacher previews not counting as attempts) read from add_questions_to_quiz::execute and quiz_tools_test."
---
# Add questions to a quiz

## Rule

`add_questions_to_quiz` SHALL add specific questions by ID, or a number of random questions drawn from a category, to a quiz, with a mark per question and a number of questions per page, and SHALL set the quiz's maximum grade to what the questions are worth together unless `max_grade` gives another value. With `make_visible` it SHALL make the quiz visible to students once the questions are in; this also needs `moodle/course:activityvisibility`. It MUST require `mod/quiz:manage` in the quiz and `moodle/question:useall` in the source bank.

## Rationale

Quizzes are the largest gain over core web services, which cannot create them at all, so the AI must be able to fill the quiz it created. The maximum grade follows the question marks because the first teacher session found Moodle scaling every mark against the default maximum behind the teacher's back, and `make_visible` came from the first full demo run so that a quiz opens to students only together with its questions. The capability checks are the ones the quiz editing screen applies, because through the AI exactly as much is possible as in the web UI.

## Scope

Applies to `add_questions_to_quiz` on a quiz in a course where the user holds `local/nitro:use`, on Moodle 5.0 or later: fixed questions from any bank the user may use, random questions from a named category of the course's shared bank or of the quiz's own bank. A quiz with student attempts is refused (the teacher's own previews do not count); removing, reordering or editing questions and reading quiz results are not covered (deferred to `nitro-pilot`).
