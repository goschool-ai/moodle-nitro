---
id: EX-01m4v5j7btcra5tkwqawsmjxg8
form: example
title: First publish
subjects:
  - BR-01m4v5j7bt6hjy4k2jpt0mkzk9
  - UC-01m4v5sy75gyt5ne3m1n3xk85j
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Create or update
      by key / Scenario: First publish
    - "local/nitro/tests/external/save_page_test.php · test_first_publish"
  quote: a teacher saves a page with key `requirements` that does not exist yet
  inferred: "Given taken from the setup of save_page_test.php::test_first_publish."
---
# First publish

## Given

A teacher who holds `local/nitro:use` and may manage activities in a course where no activity has the key `requirements`.

## When

a teacher saves a page with key `requirements` that does not exist yet

## Then

a page is created in the given section and the result says `created`
