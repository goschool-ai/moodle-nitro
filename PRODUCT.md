# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Moodle local plugin (`local_nitro`, PHP), Moodle 5.x. Every nitro page is Moodle-native: it is built with Mustache templates and the Moodle output API, uses the site theme's components, and takes on the look of the faculty's own theme. The sandbox's self-service onboarding is a separate plugin (`local_nitrosandbox`) with the same approach.

## Users

- **Teachers** at Hungarian universities, where a faculty or an institution often runs its own Moodle, and some institutions are moving to Moodle from other systems. They are a mixed group, and many are cautious: for plenty of them this is their first AI connector. They meet nitro in three places:
  - the consent screen, when they connect Claude or Copilot to Moodle;
  - the "My connections" page, where they see and revoke connections;
  - the sandbox signup, after the demo.

  The real work happens in the AI client's chat. On Moodle, the job is to understand what they are allowing and to feel in control.
- **Faculty Moodle administrators** install the plugin, grant `local/nitro:use`, set the allowlists and the kill switch, and check discovery. They are not always the same people as the web server operators.
- **Attendees of the live demo** (2026-10-06): from the next day they sign up on the sandbox and try nitro on their own personal demo course, with no help.
- **Students** never see a nitro interface. Messages, announcements and grades reach them through Moodle's own channels.

## Product Purpose

With nitro, a teacher can run their Moodle course from their own AI assistant (Claude, Microsoft Copilot): reading the course, sending messages to students, writing content and quizzes. It works with exactly the permissions the teacher has in the web UI, and the data goes to no third party. Success means a teacher connects by pasting one URL and signing in, sees what they are allowing, and can take it back at any time. Nothing reaches students without their approval.

## Positioning

Moodle itself becomes the MCP server, with no intermediary service. Core Moodle web services cannot create content, and a third-party connector would carry student data through another company. nitro writes with Moodle's own APIs, under the teacher's own permissions. It is part of the GoSchool product family: GoSchool is the university's AI for students, and nitro is how teachers work in their own AI client (as GoSchool's surfaces plan says: teacher work goes through MCP, in the teacher's own client).

## Operating Context

- The model workflow is a programming course on Canvas, run from Claude Code through the Canvas API. Its weekly loop:
  - who is behind;
  - individual reminders;
  - accepting submissions;
  - announcements;
  - pages and assignments republished from markdown.
- Live demo: 2026-10-06, on the sandbox moodle.tilosazai.org (Moodle 5.2.2). After that, a pilot in November.
- OAuth sign-in goes through the normal Moodle login and faculty SSO. Claude identifies itself with CIMD, and Copilot uses DCR or a manually registered client.
- Requirements and plans live in `openspec/changes/nitro-demo` and `openspec/changes/nitro-pilot`.

## Capabilities and Constraints

- nitro's own interfaces:
  - the OAuth consent screen;
  - the "My connections" page from the profile;
  - the admin settings page (allowlists, switches, discovery self-check);
  - manual client registration;
  - on the sandbox, the course created after signup.

  Everything else happens in the AI client's chat, including the confirmation previews.
- The consent screen shows the client's name and redirect host. It shows an extra warning when the client runs on the user's own computer, and it can show a notice set by the admin (on the sandbox: "no real student data").
- Every nitro string is available in Hungarian and English (`lang/hu`, `lang/en`). This is required, not optional.
- The plugin creates no new roles. `local/nitro:use` is only an entry gate.
- Decided: the official GoSchool logo appears on nitro's Moodle pages too, as a quiet mark. The faculty theme sets everything around it.

## Brand Commitments

- Name: **nitro** (plugin `local_nitro`, repository `moodle-nitro`), part of the GoSchool product family.
- Brand source: the goschool-web project (its `DESIGN.md`, and logos in `public/logo-*.svg`).
- Brand by surface:
  - **Moodle pages** (consent screen, My connections, admin settings): Moodle-native. The faculty theme leads, and the official GoSchool logo appears as a small, quiet mark with no brand fields or motifs.
  - **The sandbox's public landing and signup page:** the goschool-web brand (logo, purple field, Sora, one leading motif).
  - **Marketing** (demo slides, a possible nitro page on goschool-web): the goschool-web brand.
- Only the official logo files may be used. There must be no improvised substitute (such as a "G" letter).
- The voice, as with GoSchool, is direct, concrete and aware of evidence. The AI does not replace the teacher's judgement. Grades and messages are always decided by the teacher.

## Evidence on Hand

- The real Canvas workflow of the Advanced programming course, in the teacher's own Claude Code sessions and scripts (a private course repository).
- The sandbox: moodle.tilosazai.org, Moodle 5.2.2.
- Absences that must not be invented:
  - no pilot user and no user feedback;
  - Microsoft Copilot has not been tested end to end;
  - no faculty installation yet;
  - no customers or statistics.

## Product Principles

1. Through the AI, exactly as much is possible as in the web UI: no more and no less.
2. Nothing reaches a student without the teacher's explicit approval.
3. A cautious teacher always sees what they are allowing and where to take it back.
4. Moodle-native: it feels at home on every faculty's Moodle and passes an admin's code review.
5. The same functionality and the same product truth in Hungarian and in English.

## Accessibility & Inclusion

- Meets Moodle's own accessibility requirements: semantic structure, keyboard use, visible focus and sufficient contrast, in the site theme.
- Full Hungarian and English localisation for every nitro string.
