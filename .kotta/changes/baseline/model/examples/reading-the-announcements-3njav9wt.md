---
id: EX-01m4v5j7bt6ckrn4hb3njav9wt
form: example
title: Reading the announcements
subjects:
  - BR-01m4v5j7bt6kkmgxn09t55tm0k
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: Read forum discussions /
      Scenario: Reading the announcements
    - "local/nitro/tests/external/list_forum_posts_test.php · test_announcements_with_replies"
  quote: a teacher asks what was announced in their course
  inferred: "Given taken from the setup of list_forum_posts_test.php::test_announcements_with_replies."
---
# Reading the announcements

## Given

The course's announcements forum has discussions posted by the teacher, who holds `local/nitro:use`.

## When

a teacher asks what was announced in their course

## Then

the discussions of the announcements forum are returned with subject, author, time and text
