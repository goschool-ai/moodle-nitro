---
id: EX-01m4v5j7bt4argq53ex63xyxa2
form: example
title: A preview invents no identifiers
subjects:
  - BR-01m4v5j7btn2q01dc6eskr8h9g
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Dry run for every
      write tool / Scenario: A preview invents no identifiers
    - "local/nitro/tests/local/dry_run_test.php · test_dry_run_changes_nothing_but_reports_effective_settings"
    - "local/nitro/classes/local/dry_run.php · dry_run::run"
  quote: 'a dry run would create an activity, a question or a category'
  inferred: "Given: read from the dry-run tests."
---
# A preview invents no identifiers

## Given

A teacher may manage activities in a course through nitro, and the activity, question or category the call names does not exist yet.

## When

a dry run would create an activity, a question or a category

## Then

the result carries no ID or URL for it, and says that nothing was saved and that a real run creates the records
