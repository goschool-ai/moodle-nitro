---
id: BR-01m4v5j7bt1c9afnvsv8z4wpb4
form: business-rule
title: Import questions
capability: quiz-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/quiz-authoring/spec.md · Requirement: Import questions'
    - "openspec/specs/quiz-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D7. Questions through Moodle's importers"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (coarse, forgiving tools)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 8.1"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals"
    - "local/nitro/classes/external/import_questions.php · execute"
    - "local/nitro/classes/local/qbank.php · bank, shared_bank, category"
    - "local/nitro/tests/external/quiz_tools_test.php · test_gift_into_new_shared_bank, test_parse_error_imports_nothing, test_missing_permission"
  quote: >-
    `import_questions` SHALL import questions given in GIFT or Moodle XML into a
    named category of either the course's shared question bank or a quiz's own
    bank, using Moodle's own importers,
  inferred: "The moodle/question:add check and the single transaction read from import_questions::execute."
---
# Import questions

## Rule

`import_questions` SHALL import questions given in GIFT or Moodle XML into a named category of either the course's shared question bank or a quiz's own bank, using Moodle's own importers, so that the same validation applies as in the web UI import. The category SHALL be created if it does not exist. The result SHALL list the IDs and names of the imported questions.

## Rationale

Core web services cannot create questions at all. Moodle's own GIFT and XML importers give the same validation as the web UI import and support every question type they support without per-type code (D7), and the category is created on import so the tools stay forgiving for an AI working without client-side skills.

## Scope

Applies to `import_questions` on Moodle 5.0 or later, for a user holding `local/nitro:use` in the course and `moodle/question:add` in the target bank: the course's shared bank (a Question bank activity, created when the course has none) or a quiz's own bank. The import is all or nothing; listing, editing or deleting questions is not covered (deferred to `nitro-pilot`).
