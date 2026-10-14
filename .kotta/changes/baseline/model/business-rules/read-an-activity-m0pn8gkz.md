---
id: BR-01m4v5j7btb2gwm1mfm0pn8gkz
form: business-rule
title: Read an activity
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/course-read/spec.md · Requirement: Read an activity'
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.7"
    - "local/nitro/classes/external/read_activity.php · execute, execute_parameters, MAX_TEXT"
    - "local/nitro/tests/external/read_activity_test.php · test_page_content, test_activity_without_readable_text"
  quote: >-
    `read_activity` SHALL return, for one course module, its type, name,
    visibility, URL, description and — where the activity carries text of its
    own (page, book chapter list, label, assignment instructions,
  inferred: "The length limit, the visibility condition and the wording of the note read from read_activity::execute."
---
# Read an activity

## Rule

`read_activity` SHALL return, for one course module, its type, name, visibility, URL, description and — where the activity carries text of its own (page, book chapter list, label, assignment instructions, quiz or forum intro, url target) — that text, so the AI can quote or rewrite existing content instead of asking the teacher to copy it out of the browser. It SHALL return the text as plain text by default and the original HTML when `include_html` is set, SHALL list the activity's dates where it has them, and SHALL say so in a note when the activity keeps no readable text. It MUST require `local/nitro:use` in the course and the same view access as the web UI.

## Rationale

The AI should work from the course's own material, for example to write questions, a summary or an announcement from it, instead of asking the teacher to paste the text out of the browser, as the server instructions say. HTML is returned only on request, for republishing the same content, and when there is nothing to read a note tells the AI to ask the teacher for the material.

## Scope

Applies to `read_activity` for one course module of any type, for a user holding `local/nitro:use` who can see the activity (a hidden one needs `moodle/course:viewhiddenactivities`). Each text is cut at 20,000 characters; the contents of uploaded files, forum posts (`list_forum_posts`) and submissions (`list_submissions`) are not read.
