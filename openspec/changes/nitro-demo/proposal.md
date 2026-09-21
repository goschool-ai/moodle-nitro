## Why

Moodle's core web services read well but cannot create content: there is no core function that creates a page, an assignment, a question or a quiz. Web service tokens are also usually issued with a narrow function list and have to be copied by hand. Teachers who want to run their course from an AI assistant (Claude or Microsoft Copilot Cowork) therefore cannot do it on Moodle today, while the same workflow already works on Canvas through the Canvas API: a programming course on Canvas is run this way, and institutions moving from Canvas to Moodle lose it.

The tool set follows what that course actually needed in its first weeks (from the teacher's own Claude Code sessions): a daily "who is behind?" check, reminders to individual students, accepting submissions, announcements, and pages and assignments kept in sync with markdown in a repository. Quizzes were not used there, but they are the biggest gain over core web services and the strongest demo moment, so they stay.

The first deadline is a live demo on 2026-10-06, on the sandbox Moodle (moodle.tilosazai.org, Moodle 5.2). Attendees must be able to use nitro themselves from the next day (2026-10-07), on the sandbox, without anyone's help. Everything else is deferred to `nitro-pilot`.

## What Changes

- New Moodle plugin `local_nitro` (repository `moodle-nitro`) that turns Moodle itself into an MCP server, with no intermediary service.
- An MCP endpoint at `/local/nitro/mcp.php` (Streamable HTTP, JSON-RPC 2.0, `tools/list`, `tools/call`) that exposes an allowlist of tools, with guidance for the AI in tool descriptions and the server `instructions` field.
- An OAuth 2.1 authorization server inside Moodle: authorization code + PKCE; Client ID Metadata Documents (so Claude needs no registration), dynamic client registration with client secrets (as Microsoft 365 Copilot requires), and admin-registered clients; a configurable redirect allowlist with Claude and Microsoft defaults; login through the normal Moodle login (and therefore the faculty SSO); and a page in the user's profile to see and revoke connected AI tools. OAuth is mandatory because Copilot Cowork connectors do not support API keys.
- OAuth discovery served by the plugin, with an optional root `/.well-known/` rewrite and an admin self-check that shows whether it is needed.
- An access gate capability `local/nitro:use`; every tool checks the same Moodle capabilities the web UI would.
- Server-side confirmation for every write that reaches students (messages, announcements, grades): preview first, execute only with a confirmation token. `dry_run` for every write.
- An audit event for every tool call in the Moodle event log.
- Demo tool set:
  - read: `list_courses`, `course_overview`, `list_participants` (with last course access), `list_submissions` (with grading state and submitted text, links and file names)
  - content: `save_page`, `save_assignment`: create or update by a stable key, markdown in, partial updates leave other settings untouched, no silent defaults, and a report of anything Moodle's HTML cleaning removed
  - quiz: `import_questions` (GIFT / Moodle XML), `create_quiz`, `add_questions_to_quiz` (fixed and random questions)
  - students: `message_students`, `post_announcement`, `grade_submission` (points or scale, with an optional feedback comment)
  - product feedback: `send_feedback`, which mails the nitro team what a teacher found missing or broken, after the teacher approved the text
- No client-side skill packages in this change. Teacher- and course-specific rules stay in the teacher's own client (for example, their repository's skills). Claude connects as a custom connector; Copilot Cowork connects through a connector-only app manifest.
- Sandbox self-service for attendees, in a separate sandbox-only plugin: signing up on moodle.tilosazai.org gives each teacher a personal copy of the demo course with its own fictitious students, the teacher role and `local/nitro:use`.

## Capabilities

### New Capabilities
- `mcp-endpoint`: the MCP transport, tool discovery and invocation, allowlist, schema generation from external function definitions, server instructions.
- `oauth-server`: authorization code + PKCE flow; CIMD, dynamic and admin client registration; redirect allowlist; token issuance, expiry, validation and user revocation; discovery metadata.
- `access-control`: the `local/nitro:use` gate, per-call context and capability checks, data minimisation of read results, site-level kill switch, consent-screen notice, discovery self-check.
- `write-confirmation`: `dry_run` for all writes; preview-then-confirm for writes that reach students.
- `audit-log`: a Moodle event for every tool call.
- `course-read`: the teacher's courses, course overview, participants with last access, assignment submissions with their content and grading state, and forum discussions with their posts.
- `content-authoring`: creating and updating pages and assignments from markdown by a stable key.
- `quiz-authoring`: importing questions into a question bank, creating quizzes, adding fixed and random questions.
- `messaging`: messages to individual students and course announcements.
- `grading`: grading assignment submissions with an optional feedback comment.
- `sandbox-onboarding`: self-service signup on the sandbox that creates a personal demo course with isolated fictitious students.
- `feedback`: a tool that mails feedback about nitro to the address the admin configured, after the teacher approved the text.

### Modified Capabilities
<!-- none: the project has no specs yet -->

## Non-goals

- Course creation and enrolment (handled by the student information system), admin operations, an AI running inside Moodle, automatic grading without human approval.
- In `nitro-pilot`: file and PDF upload, labels, URLs, sections, visibility, forums, course front page, inbox reading, calendar, extensions, question editing, quiz results, completion, Moodle 4.5 support, client-side skill packages, admin view of all issued tokens.
- Real student data on the sandbox: the sandbox has no data protection agreement with any university and holds fictitious students only.

## Impact

- New code: `local/nitro` plugin (`version.php`, `db/access.php`, `db/services.php`, `db/install.xml`, `db/events`, `classes/external/*`, `classes/mcp/*`, `classes/oauth/*`, `mcp.php`, OAuth endpoints, settings page, profile page).
- New code, sandbox only: `local/nitrosandbox` plugin and a template demo course backup.
- New database tables for OAuth clients, authorization codes, tokens and pending confirmations.
- Security: an OAuth server with open client registration is public on the internet from 2026-10-06, so the OAuth endpoints get a security review before 2026-10-02.
- Depends on Moodle 5.0+ APIs: `add_moduleinfo()` / `update_moduleinfo()`, `qformat_gift` / `qformat_xml`, `mod_qbank`, `mod_quiz\structure`, the assign grading API, the forum and message APIs.
- Deployment: the sandbox moodle.tilosazai.org, a machine we control with a fixed IP, currently behind Cloudflare for convenience. A Microsoft 365 tenant with Copilot licences is needed to test Cowork; without it the demo runs on Claude, and Copilot support is presented as standards-based and to be verified in the pilot.
