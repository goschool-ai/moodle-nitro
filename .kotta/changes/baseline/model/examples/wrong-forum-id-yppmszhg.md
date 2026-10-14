---
id: EX-01m4v5zbqcd3vayn9jyppmszhg
form: example
title: Wrong forum ID
subjects:
  - BR-01m4v5zabe039r7af5sajtd3h1
capability: course-read
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/list_forum_posts_test.php · test_wrong_cmid_names_the_forums"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Wrong forum ID

## Given

A course has a forum called "Közlemények" and a page; the teacher holds `local/nitro:use` and can read the forum.

## When

`list_forum_posts` is called with the page's course module ID

## Then

the call is refused and the error names "Közlemények" with its course module ID
