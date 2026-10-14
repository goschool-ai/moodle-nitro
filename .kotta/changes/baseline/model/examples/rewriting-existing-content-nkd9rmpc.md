---
id: EX-01m4v5j7btmpx7vx93nkd9rmpc
form: example
title: Rewriting existing content
subjects:
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read an activity /
      Scenario: Rewriting existing content
    - "local/nitro/tests/external/read_activity_test.php · test_page_content"
  quote: the AI is asked to extend a page it did not write
  inferred: "Given taken from the setup of read_activity_test.php::test_page_content."
---
# Rewriting existing content

## Given

A page whose content was not written through nitro exists in the course; the teacher holds `local/nitro:use` and may manage activities.

## When

the AI is asked to extend a page it did not write

## Then

it can read the current content with `include_html` and save the edited HTML back with `save_page`
