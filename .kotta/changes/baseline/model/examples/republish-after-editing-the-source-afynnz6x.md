---
id: EX-01m4v5j7bt85wz40n7afynnz6x
form: example
title: Republish after editing the source
subjects:
  - BR-01m4v5j7bt6hjy4k2jpt0mkzk9
  - UC-01m4v5sy75gyt5ne3m1n3xk85j
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Create or update
      by key / Scenario: Republish after editing the source
    - "local/nitro/tests/external/save_page_test.php · test_republish_updates_without_duplicate"
  quote: the teacher saves the page with key `requirements` again with new content
  inferred: "Given taken from the setup of save_page_test.php::test_republish_updates_without_duplicate."
---
# Republish after editing the source

## Given

A page with the key `requirements` exists in the course, created by an earlier `save_page` call.

## When

the teacher saves the page with key `requirements` again with new content

## Then

the existing page's content is replaced, no second page appears, and the result says `updated`
