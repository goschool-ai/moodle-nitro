# nitro

A Moodle plugin (`local_nitro`), part of the GoSchool product family, that lets teachers run their course from an AI assistant such as Claude or Microsoft Copilot Cowork. The assistant can read the course, send messages to students, and write content and quizzes. It does this with the teacher's own Moodle permissions, and with no service in between: the data goes straight between Moodle and the AI client the teacher already uses, never through GoSchool. What the teacher sends that client is, of course, processed by whoever runs it (Anthropic for Claude, Microsoft for Copilot).

> **Status:** in planning. No code yet. The first milestone is a live demo on 2026-10-06.

## Why

Moodle's core web services are good at reading but cannot create content: no page, assignment, question or quiz. Web service tokens are also usually issued with a narrow function list and have to be copied by hand. On Canvas this workflow already works through the Canvas API; nitro brings it to Moodle.

## How it works

Moodle itself is the MCP server. There is no intermediary service.

```
 Claude / Copilot Cowork
        |  MCP (JSON-RPC over HTTPS) + OAuth bearer token
        v
 /local/nitro/mcp.php
        |  runs as the logged-in teacher
        v
 tools = Moodle external functions
   - own:  pages, assignments, question import, quizzes, messages
   - core: enrolments, submissions, completion
        |
        v
 Moodle APIs and database
```

- **OAuth 2.1 inside Moodle:** authorization code + PKCE. Claude identifies itself with a Client ID Metadata Document, so it needs no registration. Microsoft 365 Copilot uses dynamic client registration with a client secret, or a client registered by the admin. The teacher pastes one URL and logs in once through the normal Moodle login (including single sign-on). Nobody copies a long-lived token.
- **Same permissions as the web UI:** every tool checks the Moodle capabilities the equivalent UI action requires. The only new capability is `local/nitro:use`, an entry gate that admins grant per site, category or course.
- **Human approval enforced on the server:** messages, announcements and grades first return a preview. They run only when repeated with a confirmation token. Every write tool also supports `dry_run`.
- **Audit:** every tool call is recorded in the Moodle event log, without message or content bodies.
- **No client-side package needed:** usage guidance for the AI lives in the tool descriptions and the MCP server instructions. Teacher-specific rules stay in the teacher's own client.
- **Hungarian and English:** every screen and message of the plugin exists in both languages.

## Planned tools

| Group | Demo (`nitro-demo`) | Pilot (`nitro-pilot`) |
| --- | --- | --- |
| Read | `list_courses`, `course_overview`, `list_participants` (with last access), `list_submissions` (with content and grading state) | `completion_status`, inbox replies, calendar |
| Content | `save_page`, `save_assignment` (create or update by key, from markdown) | file and PDF upload, `save_url`, `save_label`, sections, visibility, forum, course front page |
| Quiz and question bank | `import_questions` (GIFT, Moodle XML), `create_quiz`, `add_questions_to_quiz` | `list_questions`, `update_question`, `quiz_results` |
| Students (with approval) | `message_students`, `post_announcement`, `grade_submission` | `grant_extension`, `add_feedback_comment` |

The demo tool set follows a real teaching week, taken from a course already run this way on Canvas: check who is behind, remind them one by one, accept submissions, post an announcement, and republish pages and assignments from markdown. Quizzes are the extra that core Moodle web services cannot do at all.

## Requirements

- Moodle 5.0 or later (Moodle 4.5 support is planned for the pilot)
- HTTPS. The plugin serves the OAuth discovery documents from its own paths. Microsoft 365 Copilot most likely also needs a one-line web server rewrite for the root `/.well-known/` URLs; the plugin's settings page checks this and prints the rule.
- Claude: a plan that supports custom connectors. Copilot Cowork: a Microsoft 365 Copilot licence (Copilot Chat is not enough).

## Installation (planned)

1. Copy the plugin to `local/nitro` and run the Moodle upgrade.
2. Grant `local/nitro:use` to the teachers or courses that should have access.
3. Optionally, under *Site administration > Plugins > Local plugins > nitro*, adjust the allowed redirect URIs (Claude, Microsoft 365 and local clients by default), switch dynamic registration off, or register clients by hand.
4. The teacher adds `https://<moodle>/local/nitro/mcp.php` as a connector in their AI tool and logs in with their Moodle account. They can see and revoke connected tools on their profile.

## Try it on the sandbox

From 2026-10-07, teachers can try nitro themselves on the sandbox, moodle.tilosazai.org. Signing up creates a personal demo course with fictitious students and submissions. After that, add the connector URL above to Claude. The sandbox is for trying things out only: do not put real student data on it.

## Specs and roadmap

The project is planned with [OpenSpec](https://github.com/Fission-AI/OpenSpec).

- [`openspec/specs/`](openspec/specs/): what nitro does now, one spec per capability (OAuth server, MCP endpoint, access control, the tools, sandbox onboarding).
- [`nitro-demo`](openspec/changes/archive/2026-09-22-nitro-demo/): the finished first round, with its proposal, design and tasks. What is left before the demo is in [`demo/checklist.md`](demo/checklist.md).
- [`nitro-pilot`](openspec/changes/nitro-pilot/): the rest of the scope for the pilot. For now this is a proposal only.

| Date | Milestone | Done when |
| --- | --- | --- |
| 2026-09-25 | Skeleton | MCP endpoint with OAuth runs on the sandbox, Claude connects, read tools work |
| 2026-10-02 | Demo-ready | content, quiz, messaging, announcement and grading tools work; the demo script runs cleanly three times |
| 2026-10-06 | Live demo | live demo on moodle.tilosazai.org, needs list from teachers |
| 2026-10-07 | Sandbox open | attendees sign up and connect on their own, without admin help |
| 2026-11 | Pilot | installed on one institution's Moodle, 3–5 teachers, after code review |

## Not in scope

Course creation and enrolment (handled by the student information system), admin operations, an AI running inside Moodle, grading without human approval, profiling students.

## Author

Árpád Tamási

## License

GNU GPL v3 or later, as required for Moodle plugins.
