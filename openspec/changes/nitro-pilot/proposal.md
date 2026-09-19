## Why

`nitro-demo` delivers the 2026-10-06 live demo and the self-service sandbox. A pilot (target: November 2026, one institution's Moodle, 3–5 teachers, after code review) needs the rest of a teacher's week, a production-grade OAuth setup, Microsoft 365 Copilot verified end to end, and support for the Moodle versions faculties actually run.

Audiences:
- **Universities where a faculty or institution runs its own Moodle** and teachers have Microsoft Copilot or Claude. The pilot runs on one such Moodle.
- **Institutions moving from Canvas to Moodle.** The model workflow (a course run through the Canvas API) is lost in the move, and `local_nitro` gives it back on Moodle. A new installation is also more open to new plugins than an established one, and Canvas-to-Moodle content migration is a natural use case for the content tools.

## What Changes

- **Files:** `upload_file` / `save_resource` for slide PDFs and handouts, and images embedded in page content (draft-area upload), the most visible gap in the teacher's Canvas workflow.
- **Course structure:** `save_url`, `save_label`, `set_section`, `set_visibility` (activities and the course itself), a discussion forum, the course summary / front page.
- **Reading:** `completion_status`, reading replies in the teacher's Moodle inbox, calendar events.
- **Grading extras:** `grant_extension` (per-student deadlines, via user overrides), `add_feedback_comment` without a grade. (`grade_submission` ships in `nitro-demo`.)
- **Question bank and quiz analysis:** `list_questions`, `update_question` (a new question version, earlier attempts untouched), `quiz_results` (attempts, scores, per-question facility and discrimination index).
- **Moodle 4.5 support:** the course-context question bank branch for sites that have not moved to 5.x; both branches tested.
- **Microsoft 365 Copilot verified:** a Cowork connector package built with Agents Toolkit against a faculty Moodle, with DCR or a hand-registered client, tested in a tenant with Copilot licences.
- **OAuth for production:** an admin view of all issued tokens with revocation, and tuning of lifetimes and the never-used client cap from the sandbox experience. (CIMD, DCR and the user's own connections page ship in `nitro-demo`.)
- **Client packages (optional):** generic `SKILL.md` skills as a Claude plugin, only if the demo and sandbox show that tool descriptions alone are not enough. Teacher-specific rules stay in each teacher's own client.
- **Pilot readiness:** installation guide for faculty admins (including the root well-known rewrite for Copilot), open-source release, code review package, needs list collected at the demo.

## Capabilities

### New Capabilities
- `file-resources`: uploading files as course resources and images into content.

### Modified Capabilities
These capabilities are introduced by `nitro-demo`; this change extends them after `nitro-demo` is archived.
- `oauth-server`: admin view and revocation of all issued tokens.
- `course-read`: completion status, inbox replies, calendar.
- `content-authoring`: URL, label, section, visibility, forum and course summary tools.
- `grading`: extensions and feedback comments without a grade.
- `quiz-authoring`: question search and editing, quiz results and statistics, Moodle 4.5 question bank.
- `write-confirmation`: extensions join the confirmation protocol.

## Non-goals

Course creation and enrolment (handled by the student information system), admin operations, an AI running inside Moodle, automatic grading without human approval, profiling tools. When the AI groups students by behaviour, the grouping happens in the conversation from that course's data only, and is never stored as a label.

## Open Questions

- Which faculties run Moodle, and on which versions? (asked from the pilot contact)
- Do teachers have Microsoft 365 Copilot licences and Cowork? (asked from the pilot contact)
- What does the institution's policy allow to be sent to an AI tool? (asked from the pilot contact)
- Which AI tool do teachers actually have: M365 Copilot, only Copilot Chat (not enough for Cowork), Claude, or none? Measured on the demo needs list. If most have only Copilot Chat or nothing, a GoSchool-hosted client (GoSchool is already a data processor for universities) becomes a separate product decision; nitro itself does not change, GoSchool would be one more OAuth client.
- Is a detailed access log (who opened what) needed for grouping students by behaviour, or are submissions and completion enough?
- For institutions moving from Canvas: November pilot or later? Who runs their Moodle, centrally or per faculty, and when does Canvas shut down?

## Impact

- Extends `local_nitro` with new external functions, an admin token page, file handling and version-dependent question bank code.
- Needs a faculty admin willing to install and review the plugin (and a web server change for Copilot discovery), and a Microsoft 365 tenant with Copilot licences.
