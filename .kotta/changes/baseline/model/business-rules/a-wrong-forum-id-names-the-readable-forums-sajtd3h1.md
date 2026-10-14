---
id: BR-01m4v5zabe039r7af5sajtd3h1
form: business-rule
title: A wrong forum ID names the readable forums
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path (a wrong cmid for list_forum_posts)"
    - "local/nitro/classes/external/list_forum_posts.php · execute"
    - "local/nitro/tests/external/list_forum_posts_test.php · test_wrong_cmid_names_the_forums, test_hidden_forum_is_not_returned"
  inferred: "The behaviour and reason are stated in design.md's Attendee path; the treatment of hidden forums is read from list_forum_posts::execute."
---
# A wrong forum ID names the readable forums

## Rule

When `list_forum_posts` is given a course module ID that is not a forum the user can read in the course, it MUST return nothing from it and SHALL answer with an error that names the forums the user can read there with their course module IDs, or says that there is none.

## Rationale

A wrong course module ID is an easy mistake for the AI; naming the readable forums lets it correct itself on the next call.

## Scope

Applies to `list_forum_posts` called with `cmid`. A hidden forum, or one without `mod/forum:viewdiscussion`, counts as not readable and is not named.
