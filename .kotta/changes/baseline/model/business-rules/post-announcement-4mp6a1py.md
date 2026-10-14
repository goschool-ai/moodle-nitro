---
id: BR-01m4v5j7btbvevrsab4mp6a1py
form: business-rule
title: Post announcement
capability: messaging
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/messaging/spec.md · Requirement: Post announcement'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why (announcements in the weekly loop)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D11. Grades and announcements through Moodle''s own APIs'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens'
    - 'local/nitro/classes/external/post_announcement.php · execute()'
  quote: >-
    `post_announcement` SHALL post a discussion with a subject and a markdown
    message to the course's announcements forum, creating that forum if the
    course has none, so that Moodle's usual forum
---
# Post announcement

## Rule

`post_announcement` SHALL post a discussion with a subject and a markdown message to the course's announcements forum, creating that forum if the course has none, so that Moodle's usual forum notifications go out. It MUST require `mod/forum:addnews` and is subject to the confirmation protocol of `write-confirmation`.

## Rationale

Announcements are part of the teacher's weekly loop (proposal, Why). Posting through `forum_get_course_forum()` and the forum post API creates the announcements forum when it is missing and lets Moodle's cron send the usual notifications, as a post from the web UI would (D11). It reaches students, so it waits for the teacher's approval (D5).

## Scope

`post_announcement` in one course's announcements (news) forum. Notifications go out on Moodle's next scheduled run after the editing time. The preview warns when the course is hidden, when the forum will be created, and when fewer people get mail than can read the course. Other forums and replies are not covered (forums are deferred to `nitro-pilot`).
