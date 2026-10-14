---
id: EX-01m4v5j7btjkydvcfefjhpshea
form: example
title: Reading a page
subjects:
  - BR-01m4v5j7btb2gwm1mfm0pn8gkz
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read an activity /
      Scenario: Reading a page
    - "local/nitro/tests/external/read_activity_test.php · test_page_content"
  quote: a teacher asks what is on the week 4 page
  inferred: "Given taken from the setup of read_activity_test.php::test_page_content."
---
# Reading a page

## Given

A page with the key `week4`, whose content was saved from markdown, exists in a course where the teacher holds `local/nitro:use`.

## When

a teacher asks what is on the week 4 page

## Then

the page's content is returned as text
