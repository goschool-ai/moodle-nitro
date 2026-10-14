---
id: BR-01m4v5j7bt6kkmgxn09t55tm0k
form: business-rule
title: Read forum discussions
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-proposed-human-approved
  sources:
    - ".kotta/changes/baseline/conversation.md · J1"
    - 'openspec/specs/course-read/spec.md · Requirement: Read forum discussions'
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "PRODUCT.md · Product Principles"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path (list_forum_posts added in a later version)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.6"
    - "local/nitro/classes/external/list_forum_posts.php · execute, MAX_TEXT"
    - "local/nitro/tests/external/list_forum_posts_test.php · test_one_forum_and_limits"
  quote: >-
    `list_forum_posts` SHALL return the discussions of a course's forums with
    their posts: forum name, discussion subject, author, time, and the message
    as text, newest discussion first.
  inferred: "The default and maximum limits and the text length read from list_forum_posts::execute. Keeping the limits and reporting what was left out is the product owner's choice (J1, answer 3)."
---
# Read forum discussions

## Rule

`list_forum_posts` SHALL return the discussions of a course's forums with their posts: forum name, discussion subject, author, time, and the message as text, newest discussion first. It SHALL take either a course (all forums the user may see) or one forum by course module ID, and SHALL limit how many discussions and posts it returns. When a forum has more discussions, or a discussion more posts, than it returned, or a post was cut, the result SHALL say so. It MUST apply the same access rules as the web UI (`mod/forum:viewdiscussion` per forum, group mode, and posts hidden until a user has posted in a Q&A forum are not returned).

## Rationale

The limits keep a reply small enough for the AI to work with; saying what was left out means neither the AI nor the teacher mistakes a partial answer for the whole forum. A course's news and student questions live in its forums, while `course_overview` only says that a forum exists, so the AI reads announcements and discussions here, as the server instructions explain. It follows the web UI's access rules because through the AI the teacher sees exactly what they see in the browser.

## Scope

Applies to `list_forum_posts` for a course where the user holds `local/nitro:use`: every forum they can read, announcements included, or one forum by course module ID. By default it returns 10 discussions per forum and 20 posts per discussion (at most 100 and 200), posts oldest first, each text cut at 5,000 characters; posting is `post_announcement` (messaging).
