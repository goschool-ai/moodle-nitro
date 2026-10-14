---
id: BR-01m4v5j7btkm0hyg6h2p15v1y5
form: business-rule
title: Supported Moodle versions
capability: quiz-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Supported Moodle
      versions
    - "openspec/specs/quiz-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D7. Questions through Moodle's importers"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Internal quiz APIs differ between 5.0 and 5.1)"
    - "local/nitro/README.md · 1. Requirements"
    - "local/nitro/classes/local/qbank.php · require_moodle_5"
    - "local/nitro/tests/external/quiz_tools_test.php · test_moodle_before_5"
  quote: The quiz tools SHALL work on Moodle 5.0 and later.
  inferred: "Which tools carry the guard read from the callers of qbank::require_moodle_5."
---
# Supported Moodle versions

## Rule

The quiz tools SHALL work on Moodle 5.0 and later. On earlier versions they MUST fail with a clear error instead of acting on the pre-5.0 question bank.

## Rationale

The quiz tools are built on the Moodle 5.x question bank model, where a course's shared bank is a Question bank activity, and the internal quiz APIs differ between versions, so the design adds version checks. On an older site the tools refuse with a clear error instead of acting on a bank model they were not built for; faculty Moodles on 4.5 are a `nitro-pilot` item.

## Scope

Applies to `import_questions`, `create_quiz` and `add_questions_to_quiz`, which check the site's Moodle branch before anything else. The other tools carry no version guard of their own, although the admin guide requires Moodle 5.0 or later for the whole plugin.
