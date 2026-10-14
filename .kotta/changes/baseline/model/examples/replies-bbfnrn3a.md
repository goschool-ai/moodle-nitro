---
id: EX-01m4v5j7btx688tcvzbbfnrn3a
form: example
title: Replies
subjects:
  - BR-01m4v5j7bt6kkmgxn09t55tm0k
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read forum discussions /
      Scenario: Replies
    - "local/nitro/tests/external/list_forum_posts_test.php · test_announcements_with_replies"
  quote: a discussion has replies
  inferred: "Given taken from the setup of list_forum_posts_test.php::test_announcements_with_replies."
---
# Replies

## Given

A discussion in a forum the teacher can read has an opening post by a student and a reply by the teacher.

## When

a discussion has replies

## Then

the replies are returned under their discussion, in order, with their authors
