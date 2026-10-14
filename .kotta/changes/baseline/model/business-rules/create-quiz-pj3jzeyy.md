---
id: BR-01m4v5j7btg9px95xnpj3jzeyy
form: business-rule
title: Create quiz
capability: quiz-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/quiz-authoring/spec.md · Requirement: Create quiz'
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Without skills, the AI may chain quiz tools wrongly)"
    - "PRODUCT.md · Product Principles"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 8.2"
    - "local/nitro/classes/external/create_quiz.php · execute, defaults, apply_review_preset"
    - "local/nitro/tests/external/quiz_tools_test.php · test_practice_quiz_until_friday, test_exam_preset_hides_answers_until_close"
  quote: >-
    `create_quiz` SHALL create a Quiz activity in a course section with a name,
    markdown introduction, optional open and close times, time limit, allowed
    attempts, question order shuffling and a review-options
  inferred: "Linking the two presets to the design's 'coarse, forgiving tools' answer is my reading of design.md; site defaults for unset settings read from create_quiz::defaults."
---
# Create quiz

## Rule

`create_quiz` SHALL create a Quiz activity in a course section with a name, markdown introduction, optional open and close times, time limit, allowed attempts, question order shuffling and a review-options preset (`practice`: answers and feedback shown after each attempt, or `exam`: shown after the quiz closes). It SHALL return the module ID and URL. It MUST require `moodle/course:manageactivities` and `mod/quiz:addinstance`.

## Rationale

Quizzes are the largest gain over core web services and the strongest demo moment, so creating one stays in the tool set although the model Canvas course did not use quizzes. Review behaviour is offered as two presets rather than Moodle's full review matrix, in line with the design's answer to the risk that an AI without client-side skills chains the quiz tools wrongly: coarse, forgiving tools. The capability checks are those of the web UI.

## Scope

Applies to `create_quiz` in a course where the user holds `local/nitro:use`, on Moodle 5.0 or later. It only creates (no update by key), and every setting it does not take comes from the site's quiz defaults. Adding questions is `add_questions_to_quiz`; quiz results and question editing are not covered (deferred to `nitro-pilot`).
