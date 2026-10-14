---
id: EX-01m4v5j7bt52dbhhatnk7d90ff
form: example
title: Submitted repository links
subjects:
  - BR-01m4v5j7bt812y1xxt7wwqq2w5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions / Scenario: Submitted repository links
    - "local/nitro/tests/external/list_submissions_test.php · test_repository_links"
  quote: >-
    students submitted an online text containing a repository URL and the
    teacher sets `include_content`
  inferred: "Given taken from the setup of list_submissions_test.php::test_repository_links."
---
# Submitted repository links

## Given

An assignment with online text submission, where students submitted text containing repository links, both as links and as bare URLs.

## When

students submitted an online text containing a repository URL and the teacher sets `include_content`

## Then

each entry contains the text and the URL listed as a link
