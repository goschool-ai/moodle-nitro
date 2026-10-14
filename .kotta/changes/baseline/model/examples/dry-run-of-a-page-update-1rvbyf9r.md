---
id: EX-01m4v5j7btffprpg2g1rvbyf9r
form: example
title: Dry run of a page update
subjects:
  - BR-01m4v5j7btn2q01dc6eskr8h9g
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Dry run for every
      write tool / Scenario: Dry run of a page update
    - "local/nitro/tests/external/save_page_test.php · test_dry_run_changes_nothing"
  quote: 'a teacher calls `save_page` with `dry_run: true` for an existing key'
  inferred: "Given: read from the save_page test."
---
# Dry run of a page update

## Given

A page with the given key exists in the course, and the teacher may manage activities there.

## When

a teacher calls `save_page` with `dry_run: true` for an existing key

## Then

nothing is changed and the result says the page would be updated, lists the fields that would change, and reports any content Moodle's HTML cleaning would remove
