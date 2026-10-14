---
id: EX-01m4v5j7bt72cn571vx3f8s45z
form: example
title: Headings and lists
subjects:
  - BR-01m4v5j7btg43hqqy6ypbhb7xa
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Markdown input
      with a cleaning report / Scenario: Headings and lists
    - "local/nitro/tests/local/content_test.php · test_headings_table_and_list"
  quote: >-
    a page is saved with markdown containing headings, a table and a bulleted
    list
  inferred: "Given taken from the setup of content_test.php::test_headings_table_and_list."
---
# Headings and lists

## Given

A teacher who holds `local/nitro:use` and may manage activities saves page content written in markdown.

## When

a page is saved with markdown containing headings, a table and a bulleted list

## Then

the stored content contains the corresponding HTML elements
