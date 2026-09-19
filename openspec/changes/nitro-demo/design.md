## Context

See proposal.md for motivation and scope. Requirements are in `specs/`.

Constraints that shape the approach:

- Moodle core web services read well but have no functions that create modules, questions or quizzes. Writing content needs Moodle's internal PHP APIs, so the plugin must run inside Moodle.
- Copilot Cowork connectors accept only OAuth (dynamic or static client registration), not API keys. Claude custom connectors accept OAuth as well. Moodle has no OAuth authorization server (its `oauth2` subsystem is a client only).
- The demo sandbox (moodle.tilosazai.org) runs Moodle 5.2.2 with the `public/` web root, on a machine we control with a fixed IP, currently behind Cloudflare for convenience. Faculty Moodles may run 4.5; that is handled in `nitro-pilot`.
- The tool set is driven by the teacher's real Canvas workflow on Advanced programming (ELTE): daily status checks, individual reminders, accepting submissions, announcements, and pages and assignments republished from markdown in a repository. Teacher- and course-specific rules (course constants, "never grade in sync", feedback style) live in the teacher's own client skills, not in nitro.
- Timeline: skeleton by 2026-09-25, demo-ready by 2026-10-02, demo on 2026-10-06, attendees self-serve on the sandbox from 2026-10-07.
- Faculty Moodle admins can install plugins but often cannot change the web server configuration; some Moodles run under a subpath or on hosted platforms.

## Goals / Non-Goals

**Goals:**
- One installable `local_nitro` plugin with no external runtime dependency.
- Every tool also usable as a normal Moodle external function, so the same code path, parameter validation and capability checks serve both MCP and classic web service calls.
- The smallest OAuth server that both Claude and Cowork accept, installable without web server changes.
- A teacher connects by pasting one URL; no admin action per teacher beyond granting `local/nitro:use`.

**Non-Goals:**
- A general-purpose OAuth/OpenID provider for Moodle.
- MCP features beyond tools (resources, prompts, sampling, SSE streaming).

## Decisions

### D1. Architecture: Moodle is the MCP server

```
 Claude / Copilot Cowork
        |  MCP (JSON-RPC over HTTPS) + Bearer token
        v
 /local/nitro/mcp.php ---- token check ---- OAuth tables
        |  set user, then per tool:
        v
 tool registry (allowlist)
        |                         \
        v                          v
 own external functions      wrapped core functions
 (content, quiz, messaging)  (enrol, assign, completion)
        |                          |
        +------> Moodle APIs <-----+
                     |
                  database
```

Each tool is a Moodle external function declared in `db/services.php` (`classes/external/*`), starting with `validate_context()` and `require_capability()`. The MCP layer is a thin adapter: it maps `tools/list` to the allowlisted functions and generates each JSON Schema from the function's `execute_parameters()` description, and it maps `tools/call` to the function call with the standard parameter validation. Wrapped core functions (for example `core_enrol_get_enrolled_users`) are called through the same mechanism, with a nitro-level wrapper where the output must be minimised or reshaped.

Alternative: tools as plain PHP classes outside the external API. Rejected, because we would lose free parameter validation and schema generation, and the tools could not be tested with standard web service tooling.

### D2. Own MCP layer instead of forking `webservice_mcp`

`webservice_mcp` is a web service *protocol* plugin: it plugs into Moodle's token-based web service stack. Our authentication is OAuth, with tokens that are not Moodle web service tokens, and we need to control discovery responses (401 + `WWW-Authenticate`) and server `instructions`. The MCP surface we need (initialize, tools/list, tools/call, notifications) is small. Decision: write our own adapter in `local_nitro`, and in the first day of work read `webservice_mcp` for its schema-generation code and edge cases, reusing ideas (with attribution, if code is copied, under GPL).

Alternative: fork `webservice_mcp` and add OAuth. Rejected for the demo because the fork would carry the web service token model we are replacing and split the code over two plugins.

### D3. OAuth 2.1 server: CIMD for Claude, DCR with secrets for Copilot

Attendees must be able to connect on their own the day after the demo, so a teacher must only paste the MCP URL into Claude. The two clients register differently (sources: Claude connector authentication docs, Microsoft 365 Copilot DCR and OAuth docs, September 2026):

```
               how it identifies itself          who triggers it      how often
 Claude        CIMD: client_id is a URL on        nobody               never registers
 (web, app)    claude.ai, redirect
               https://claude.ai/api/mcp/auth_callback
 Claude Code   CIMD, loopback redirect, any port  nobody               never registers
 Copilot       DCR, MUST get a client_secret,     the package builder  once per Moodle
 Cowork        or a hand-registered client;       (Agents Toolkit)
               redirect https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect
```

Claude prefers CIMD whenever the metadata advertises `client_id_metadata_document_supported: true` and the `none` auth method; with DCR alone it would register a new client on every fresh connection. So CIMD is the main path for Claude, DCR is kept for Copilot and other MCP clients, and admin registration for sites that switch DCR off.

Endpoints are scripts under `/local/nitro/oauth/`: `authorize.php`, `token.php`, `register.php`, `metadata.php`. Authorization code + PKCE (S256 only), refresh tokens with rotation, one scope `nitro` plus `offline_access` for refresh tokens. Login uses `require_login()`, so all configured auth plugins and SSO work without extra code. Tokens and client secrets are random 256-bit values stored as SHA-256 hashes in plugin tables (`local_nitro_client`, `local_nitro_code`, `local_nitro_token`). CIMD documents are fetched with Moodle's `\core\http_client` (which honours the site's blocked hosts), only from the CIMD host list, and cached in MUC for up to 24 hours.

Guardrails, because registration is open to the internet: a redirect URI allowlist with Claude, Microsoft and loopback defaults (port-agnostic loopback match, as Claude Code requires); a scheduled task deleting clients without a token after 7 days; and a cap on never-used clients. No per-IP rate limit: all Claude traffic comes from Anthropic's `160.79.104.0/21` range, so a per-IP limit would throttle every Claude user at once, and with the allowlist a flood of registrations can only create unusable clients that the clean-up removes. The consent screen shows the client's declared name together with its redirect host, because a self-declared name proves nothing.

The profile page `/local/nitro/connections.php` lists grants (user + client) and revokes them by deleting all their tokens.

Alternative: an existing Moodle OAuth server plugin (for example `local_oauth`). To be checked briefly on day 1, but most are OAuth 2.0 without PKCE, CIMD, DCR or RFC 8414/9728 metadata, which MCP clients need.

### D4. Discovery: plugin-served, root rewrite where possible

MCP clients discover in two steps. Step 1 (protected resource metadata) uses the `resource_metadata` URL from the 401 `WWW-Authenticate` header, which can point anywhere, so it points to `metadata.php`. Step 2 (authorization server metadata) is derived from the issuer URL. For an issuer with a path, the MCP authorization spec (2025-11-25) has clients try, in order: `/.well-known/oauth-authorization-server/<path>`, `/.well-known/openid-configuration/<path>`, `<issuer>/.well-known/openid-configuration`. Only the third is under Moodle's tree.

Decision: the issuer is the script URL `https://<site>/local/nitro/oauth/metadata.php`, so the third form is `metadata.php/.well-known/openid-configuration`. Moodle already requires slash arguments (`pluginfile.php/...`), so `metadata.php` receives the rest of the path and answers. No web server change is needed. The root well-known rewrite stays documented as an optional fallback, and the settings page self-check shows whether it is needed.

Because the third form is OpenID discovery, the document also includes the OpenID fields a strict client may check (`subject_types_supported`, `response_types_supported`), without claiming ID token support. If a client rejects the document without ID token fields, the fallback is the rewrite rule, not implementing OpenID Connect.

This depends on client behaviour. Claude's docs confirm it follows `resource_metadata` from the 401 but only say the authorization server must serve metadata "at its `/.well-known/` paths"; Microsoft's docs name the root `.well-known/oauth-protected-resource` and `.well-known/oauth-authorization-server` endpoints. So Copilot very likely needs the root rewrite, and Claude may not.

On the sandbox we control the machine (fixed IP), so the root rewrite is simply enabled and the demo does not depend on the test. The day-1 test (with Claude, Claude Code and MCP Inspector) only decides what the README tells faculty admins: "optional", "required for Copilot", or "required".

### D10. Stable keys for create-or-update

`save_page` and `save_assignment` look up the activity by `key` in the course module's `idnumber` field, which Moodle already has for every activity, is unique per course in practice, and is editable in the web UI. Absent: create with `create_module()`; present: load the current form data with `get_moduleinfo_data()`, overlay only the fields given in the call, and save with `update_moduleinfo()`. This makes republishing from the teacher's repository idempotent, like the Canvas scripts that matched pages by title, but with a key that survives renaming.

The HTML cleaning report compares the element and attribute names in the converted HTML with those left after `clean_text()`/`format_text()` cleaning, and lists the difference.

Assignments never get silent defaults: when creating, submission types and grading are mandatory parameters, and the result echoes all effective settings (a Canvas assignment created from chat once came out with no submission type and zero points).

### D11. Grades and announcements through Moodle's own APIs

`grade_submission` uses the assign API (`assign::save_grade()` via `mod_assign_save_grade` semantics) so marking workflow, notifications and the gradebook behave as in the web UI; scale grades are given by item name and mapped to the scale index. `post_announcement` uses `forum_get_course_forum($courseid, 'news')` (which creates the announcements forum when missing) and the forum post API, so the usual forum notifications are sent by Moodle's cron.

### D5. Server-side confirmation tokens

Confirmation applies to every tool whose effect reaches students: `message_students`, `post_announcement`, `grade_submission`. This mirrors the teacher's own hard rule ("show it first, send only after an explicit go"), which came from a message sent without approval. Content writes do not notify students, so they only get `dry_run`.

Confirmations are stored in `local_nitro_confirm` (user, tool, SHA-256 of canonicalised arguments, expiry, used flag). The token returned to the client is a random value; its hash is stored. The canonicalisation drops `dry_run` and the confirmation token itself and sorts keys, so that a correctly repeated call matches.

### D6. Content writes through Moodle's module APIs

Pages, assignments and quizzes are created with `create_module()` / `add_moduleinfo()` and updated with `update_moduleinfo()` (see D10), using the same form data the web UI submits, so events, completion, calendar entries for due dates and cache resets happen as usual. Markdown is converted with `markdown_to_html()` and stored as `FORMAT_HTML`.

### D7. Questions through Moodle's importers

`import_questions` feeds the text to `qformat_gift` / `qformat_xml` in the target category, which gives the same validation as the web UI import and supports every question type the importer supports without per-type code. In Moodle 5.x the course's shared bank is a `mod_qbank` instance, and a quiz's own bank is its module context. Adding questions to a quiz uses `mod_quiz\structure` / `quiz_add_quiz_question()` and `quiz_add_random_questions()` on the target version; the exact calls must be checked against the sandbox's minor version.

### D8. Audit event

One event class, `\local_nitro\event\tool_called`, triggered by the MCP adapter after each call with the outcome, plus Moodle's own events from the underlying APIs. `other` stores only tool name, client ID, outcome and affected object IDs.

### D9. Sandbox onboarding as a separate plugin

A separate plugin, `local_nitrosandbox`, installed only on the sandbox. An observer on `\core\event\user_created` (for email self-registration, after confirmation: `\core\event\user_confirmed` fits better and is checked first) queues an ad hoc task that:

1. restores the template course backup (without user data) as "Demo NN",
2. creates 20 fictitious students for this course only (`auth=nologin`, email on an unroutable domain such as `@example.invalid`), enrols them, and puts them into two groups,
3. generates submissions from a fixed pattern (on time, late, missing) with the assign generator-style APIs,
4. enrols the new user as editing teacher and assigns a role granting `local/nitro:use` in the course context.

Fresh students per course instead of shared ones: shared accounts would list every attendee's course on their profile, leaking who else signed up. The cost (20 users per attendee) is negligible for the sandbox.

Alternatives: Moodle's built-in course request (fallback B if this is not ready by 2026-10-02: empty course, no students); one shared course with an enrolment key (rejected: attendees would see each other's changes).

## Risks / Trade-offs

- [The OAuth server with open registration is public from 2026-10-06] → Keep it minimal (no implicit flow, no plain PKCE, exact redirect URI match, hashed tokens, short lifetimes), DCR guardrails from D3, tests for every negative scenario in the spec, and a security-focused review of the OAuth endpoints before 2026-10-02.
- [A malicious client registers with a misleading name] → Redirect URI allowlist; the consent screen shows the redirect host next to the name.
- [CIMD fetch used for server-side request forgery] → Fetch only from the CIMD host list, through Moodle's HTTP client with the site's blocked-hosts rules, with a short timeout and a size limit.
- [Faculty Moodles cannot add the root rewrite, and Copilot needs it] → Plugin-served discovery covers Claude if the day-1 test confirms it; for Copilot the admin self-check prints the one-line rule for the web server team.
- [The sandbox's Cloudflare in front of Moodle blocks Anthropic or Microsoft requests] → Either a Cloudflare skip rule for `160.79.104.0/21` and the Microsoft ranges on `/local/nitro/*` and `/.well-known/*`, or serve the sandbox directly on its fixed IP with its own certificate; decided on day 1.
- [Copilot DCR connectors are reported to fail silently after sideloading (Microsoft Q&A, 2026)] → Copilot support is presented as standards-based and verified in the pilot; a hand-registered client is the fallback.
- [Upsert overwrites a teacher's manual edits in Moodle] → The result says `updated` and lists the changed fields; `dry_run` shows them first. The repository stays the source of truth, as in the teacher's Canvas workflow.
- [Sandbox signup depends on outbound email] → Configure and test SMTP on the sandbox on day 1; fallback: admin-created accounts from the Klub signup list.
- [Many attendees sign up at once after the demo] → Course creation runs as an ad hoc task, so signup stays fast; check the time for 30 signups in a row on the sandbox.
- [Attendees put real student data on the sandbox] → Consent screen and login page notice; fictitious students only; sandbox data can be wiped at any time.
- [Without skills, the AI may chain quiz tools wrongly] → Rich `instructions` and tool descriptions; coarse, forgiving tools (category created on import, clear errors that say what to do next); run the demo script at least five times before 2026-10-02.
- [Internal quiz APIs differ between 5.0 and 5.1] → Pin the sandbox version for the demo, check the calls on that version, add version checks.
- [No Microsoft 365 tenant with Copilot licences] → The demo runs on Claude, Cowork is shown as a screen recording.
- [A live demo depends on network and AI latency] → Prepared course in the sandbox, recorded fallback of the full script.

## Migration Plan

New plugin, no migration. On the sandbox: copy `local/nitro` and `local/nitrosandbox`, run the upgrade, enable email self-registration, keep the default redirect and CIMD allowlists, set the consent notice, upload the template course, and add the root well-known rewrite. Rotate the sandbox admin credentials before the demo. Rollback: disable with the kill switch or uninstall the plugin, which drops its tables and invalidates all tokens.

## Open Questions

- Exact token lifetimes and the never-used client cap (the specs set upper bounds and behaviour).
- Whether the Cowork manifest is valid with only `agentConnectors` and no skill; if a skill is mandatory, add a minimal one at packaging time.
