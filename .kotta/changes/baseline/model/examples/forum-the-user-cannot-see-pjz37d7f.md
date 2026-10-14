---
id: EX-01m4v5j7btmmj8ncfppjz37d7f
form: example
title: Forum the user cannot see
subjects:
  - BR-01m4v5j7bt6kkmgxn09t55tm0k
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read forum discussions /
      Scenario: Forum the user cannot see
    - "local/nitro/tests/external/list_forum_posts_test.php · test_hidden_forum_is_not_returned"
  quote: a course contains a forum hidden from the user
  inferred: "Given taken from the setup of list_forum_posts_test.php::test_hidden_forum_is_not_returned."
---
# Forum the user cannot see

## Given

A course has a forum hidden from the calling user, who holds `local/nitro:use` but cannot see hidden activities.

## When

a course contains a forum hidden from the user

## Then

its discussions are not returned
