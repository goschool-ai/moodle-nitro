## Context

See proposal.md for motivation and scope. Requirements are in `specs/`.

Constraints that shape the approach:

- Moodle core web services read well but have no functions that create modules, questions or quizzes. Writing content needs Moodle's internal PHP APIs, so the plugin must run inside Moodle.
- Copilot Cowork connectors accept only OAuth (dynamic or static client registration), not API keys. Claude custom connectors accept OAuth as well. Moodle has no OAuth authorization server (its `oauth2` subsystem is a client only).
- The demo sandbox (moodle.tilosazai.org) runs Moodle 5.2.2 with the `public/` web root, on a machine we control (host `sbc`, Docker compose project `moodle` in `/data/moodle/wrapper`, Moodle code baked into the `moodle-local` image), published only through a Cloudflare Tunnel (`uzenofuzet-cloudflared`, shared with the kreta, dl and canvas hosts) with TLS ending at Cloudflare and `sslproxy` on. Faculty Moodles may run 4.5; that is handled in `nitro-pilot`.
- The tool set is driven by the teacher's real Canvas workflow on a programming course: daily status checks, individual reminders, accepting submissions, announcements, and pages and assignments republished from markdown in a repository. Teacher- and course-specific rules (course constants, "never grade in sync", feedback style) live in the teacher's own client skills, not in nitro.
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

Day-1 reading (2026-09-19; `onbirdev/moodle-webservice_mcp` 0.6.0 and `eledia/moodle-webservice_eledia_mcp` 1.1.0, both GPL v3). The decision stands; what we take:

- Schema generation: `webservice_mcp`'s `tool_provider::generate_schema()` walk over `external_value` / `external_single_structure` / `external_multiple_structure` (PARAM_INT → integer, PARAM_FLOAT → number, PARAM_BOOL → boolean, everything else string; `desc` → `description`; `VALUE_DEFAULT` → `default`; `NULL_ALLOWED` → `[type, "null"]`; `required` collected per object level). Fix two gaps it has: a `VALUE_REQUIRED` nested structure is never listed as required (only `external_value` sets the marker), and an empty `properties` must be encoded as `{}` (`new stdClass()`), not `[]`, or the schema is invalid JSON Schema. Tool names are our short names from the allowlist, not the Moodle function names.
- Output: `clean_returnvalue()` against `execute_returns()`, then `structuredContent` plus the same JSON as a `text` content item. The `{"result": ...}` envelope is only for 2025-03-26 clients (eledia's `protocol::uses_result_envelope()`); we return objects from every tool so no envelope is needed.
- Errors: exceptions inside `tools/call` (invalid parameters, `required_capability_exception`, `moodle_exception`) become a result with `isError: true` and a plain message, as eledia does, so the model can correct itself; `webservice_mcp` returns them as JSON-RPC `-32603` errors, which most clients show as a failed call. JSON-RPC errors stay for protocol faults only (parse error `-32700`, invalid request `-32600`, unknown method `-32601`, unknown tool `-32602`).
- Protocol version: negotiate from `initialize.params.protocolVersion` against a supported list (2025-11-25, 2025-06-18, 2025-03-26), answer with the latest when unknown; reject an unsupported `MCP-Protocol-Version` header with 400. `webservice_mcp` hard-codes 2025-03-26.
- Notifications (no `id`, including unknown ones) get 202 with an empty body (`webservice_mcp` sends 204). GET gets 405 with `Allow: POST` (no SSE); `ping` returns `{}`; `resources/list` and `prompts/list` are not advertised and get `-32601`.
- Headers: read `Authorization` from `HTTP_AUTHORIZATION` / `REDIRECT_HTTP_AUTHORIZATION` and then `getallheaders()`, because PHP-FPM and some Apache setups strip it. No `wstoken` query fallback. `mcp.php` sends no CORS headers (clients call it server to server) and does not check `Origin`: every request needs a bearer token, so DNS rebinding gains nothing, and rejecting a client that sends its own `Origin` could break Claude. The OAuth endpoints (metadata, register, token) send `Access-Control-Allow-Origin: *`, because browser-based clients such as MCP Inspector call them from the page; they use no cookies.
- Implementation notes (2026-09-19): `mcp.php` defines `WS_SERVER` (which also makes `setup.php` define `NO_MOODLE_COOKIES`), so `validate_context()` / `require_login()` behave as in a web service. Tool calls go through our own `dispatcher` instead of `external_api::call_external_function()`, which drops the debug info that names an invalid parameter unless developer debugging is on. Input schemas do not mark fields nullable (`external_value` allows null by default). Top-level optional parameters must be `VALUE_DEFAULT` (Moodle rejects `VALUE_OPTIONAL` there). The kill switch setting is `local_nitro/active`, not `enabled`: core treats a plugin's `enabled` config as plugin state and purges caches when it changes (17 s per PHPUnit test). The sandbox image has `opcache.revalidate_freq = 60`, so after copying code run `apache2ctl -k graceful` in the web container.
- Tool annotations: `readOnlyHint` for read tools, `destructiveHint` for the three confirmation tools, set explicitly per tool rather than guessed from the function name as eledia does.
- Not copied: a bug in `webservice_mcp::generate_error()` puts the request id in the `jsonrpc` field.

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

Alternative: an existing Moodle OAuth server plugin. Checked on day 1 (2026-09-19):

- `projectestac/moodle-local_oauth` (and its forks) and `enovation/moodle-local_oauth2`: wrappers around `bshaffer/oauth2-server-php` for OAuth 2.0 / OpenID login into other apps. No CIMD, no DCR, no RFC 9728 resource metadata. Not usable.
- `justinhunt/moodle-local_oauthmcp` 0.2.0 (alpha, 2026-09-03, GPL v3): an OAuth 2.1 server built for MCP plugins, with PKCE S256, CIMD, DCR, RFC 8414/9728 metadata and refresh rotation. The closest match, but not adopted as a dependency: its access tokens are real Moodle web service tokens (stored in clear in `external_tokens`, minted per consumer service), its DCR registers public clients only (Copilot needs a secret, D3), redirect URIs are any `https` URL with no allowlist, the capability is checked only in the system context, the consent screen does not show the redirect host, it has a per-IP rate limit (rejected above) and no tests. Adding the missing parts would mean changing most of it.

Reused from `local_oauthmcp` as ideas, with attribution where code is copied:

- CIMD validation: the document's `client_id` must equal the URL it was fetched from; `redirect_uris` non-empty; accept the client when `none` is its `token_endpoint_auth_method` *or* is listed in `token_endpoint_auth_methods_supported` (a real ChatGPT document prefers `private_key_jwt` but lists `none`). Fetch with a 3 s timeout and a 50 KB cap, cache failed lookups too (short TTL) so a bad URL cannot be used to make us fetch repeatedly. Unlike theirs, we do not follow redirects, because the redirect target would bypass the CIMD host list.
- Authorization: validate the client and `redirect_uri` before anything else and show errors on the Moodle page until then; only after a match redirect errors back with `state`. Return `iss` with the code (RFC 9207). Codes are deleted on redemption rather than flagged as used (their lifetime is 60 s; ours is still an open question within the spec's 10-minute bound).
- Token: PKCE check as `hash_equals(challenge, base64url(sha256(verifier)))`; refresh tokens carry a `familyid`, and reuse of a rotated token revokes the whole family; the capability is re-checked on every refresh, and losing it revokes the family.
- Loopback: the port-agnostic match compares scheme, host and path, and ignores the port only for `http` with host `127.0.0.1`, `::1` or `localhost`.
- Operations notes for the README: `CGIPassAuth On` (Apache) or `fastcgi_param HTTP_AUTHORIZATION $http_authorization;` (nginx) when the bearer header does not reach PHP; in `.htaccess` the rewrite patterns lose the leading `/` and use `[END]`; wrap in `<IfModule mod_rewrite.c>`.

### D3a. What Claude actually does (probe, 2026-09-20)

Measured on the sandbox with a real connection instead of the throwaway probe script of task 1.3: the plugin itself was deployed and the web server log captured the flow. Claude Code 2.1.278 (`User-Agent: python-httpx/0.28.1` for OAuth, `Claude-User` for MCP calls):

1. `POST /local/nitro/mcp.php` → 401, and it *does* follow the `resource_metadata` pointer: `GET <issuer>/.well-known/oauth-protected-resource`.
2. For the authorization server metadata it uses the **root insert form**, `GET /.well-known/oauth-authorization-server/local/nitro/oauth/metadata.php`, not the issuer-appended form. So the root rewrite is required for Claude, as `local_oauthmcp` reported; the plugin-served forms alone would not be enough.
3. No dynamic registration: `client_id=https://claude.ai/oauth/mcp-oauth-client-metadata` (CIMD), `redirect_uri=https://claude.ai/api/mcp/auth_callback`, `code_challenge_method=S256`, `state`, `scope=nitro offline_access`, `resource=<the MCP URL>`, `prompt=consent`.
4. Authorize → Moodle login → consent screen → code → `POST /local/nitro/oauth/token.php` → access and refresh token (the grant shows client name "Claude" from the CIMD document).
5. MCP: `initialize`, `notifications/initialized` (202), `tools/list`, `tools/call` — `list_courses` succeeded and appears in the audit log.

One request is answered with 400: the client opens with `MCP-Protocol-Version: 2026-07-28` and a `server/discover` probe (a protocol version newer than the three this server implements). The MCP spec requires 400 for an unsupported version header, the client falls back to a supported version, and everything after that works. Supporting 2026-07-28 is a `nitro-pilot` item.

### D4. Discovery: plugin-served, root rewrite where possible

MCP clients discover in two steps. Step 1 (protected resource metadata) uses the `resource_metadata` URL from the 401 `WWW-Authenticate` header, which can point anywhere, so it points to `metadata.php`. Step 2 (authorization server metadata) is derived from the issuer URL. For an issuer with a path, the MCP authorization spec (2025-11-25) has clients try, in order: `/.well-known/oauth-authorization-server/<path>`, `/.well-known/openid-configuration/<path>`, `<issuer>/.well-known/openid-configuration`. Only the third is under Moodle's tree.

Decision: the issuer is the script URL `https://<site>/local/nitro/oauth/metadata.php`, so the third form is `metadata.php/.well-known/openid-configuration`. Moodle already requires slash arguments (`pluginfile.php/...`), so `metadata.php` receives the rest of the path and answers. No web server change is needed. The root well-known rewrite stays documented as an optional fallback, and the settings page self-check shows whether it is needed.

Because the third form is OpenID discovery, the document also includes the OpenID fields a strict client may check (`subject_types_supported`, `response_types_supported`), without claiming ID token support. If a client rejects the document without ID token fields, the fallback is the rewrite rule, not implementing OpenID Connect.

This depends on client behaviour. Claude's docs confirm it follows `resource_metadata` from the 401 but only say the authorization server must serve metadata "at its `/.well-known/` paths"; Microsoft's docs name the root `.well-known/oauth-protected-resource` and `.well-known/oauth-authorization-server` endpoints. So Copilot very likely needs the root rewrite, and Claude may not.

Prior evidence, to be confirmed by the probe (1.3): `local_oauthmcp` reports (tested 2026-08-29) that Claude.ai and Gemini Spark request the root *insert* form (`/.well-known/oauth-protected-resource/<resource path>` and `/.well-known/oauth-authorization-server/<issuer path>`) and do not follow the `resource_metadata` pointer or the path-appended form, while ChatGPT requests `<resource>/.well-known/openid-configuration` (appended to the MCP URL, not the issuer). If the probe confirms this, the README says the root rewrite is required for Claude, and `mcp.php` also answers the appended form from `PATH_INFO`.

On the sandbox we control the web server (Apache in the `moodle-local` image, `AllowOverride All` on `public/`), so the root rewrite is simply enabled and the demo does not depend on the test. The day-1 test (with Claude, Claude Code and MCP Inspector) only decides what the README tells faculty admins: "optional", "required for Copilot", or "required".

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

A separate plugin, `local_nitrosandbox`, installed only on the sandbox. Moodle 5.2 has no event for email confirmation (the confirm page only sets `user.confirmed` and logs the user in), so observers on `\core\event\user_loggedin` and `\core\event\user_created` queue an ad hoc task for a confirmed user without a demo course yet (a user preference marks it, so it happens once). The task:

1. restores the template course backup (without user data) as "Demo NN",
2. creates 20 fictitious students for this course only (`auth=nologin`, email on an unroutable domain such as `@example.invalid`), enrols them, and puts them into two groups,
3. generates submissions from a fixed pattern (on time, late, missing) with the assign generator-style APIs,
4. enrols the new user as editing teacher and assigns a role granting `local/nitro:use` in the course context.

Implementation notes (2026-09-20): the template is built by `local/nitrosandbox/cli/build_template.php` with nitro's own `save_page` / `save_assignment` and saved as a backup in the plugin's file area; the demo assignment's dates are moved at provisioning so that the due date has just passed and the cut-off is 3 days ahead (4 missing, 4 late, 12 on time of which 2 graded, 3 never opened the course). The gate is a sandbox-only role `nitrodemo` (created by the sandbox plugin, never by `local_nitro`). Fictitious students get no usable password; Moodle's `email_to_user()` skips `nologin` accounts, so no email leaves for them. Provisioning takes about 20 s on the dev site; with the sandbox cron running every minute, signup to ready course is up to about 80 s (measured on the sandbox in 10.6).

Fresh students per course instead of shared ones: shared accounts would list every attendee's course on their profile, leaking who else signed up. The cost (20 users per attendee) is negligible for the sandbox.

Alternatives: Moodle's built-in course request (fallback B if this is not ready by 2026-10-02: empty course, no students); one shared course with an enrolment key (rejected: attendees would see each other's changes).

## Risks / Trade-offs

- [The OAuth server with open registration is public from 2026-10-06] → Keep it minimal (no implicit flow, no plain PKCE, exact redirect URI match, hashed tokens, short lifetimes), DCR guardrails from D3, tests for every negative scenario in the spec, and a security-focused review of the OAuth endpoints before 2026-10-02.
- [A malicious client registers with a misleading name] → Redirect URI allowlist; the consent screen shows the redirect host next to the name.
- [CIMD fetch used for server-side request forgery] → Fetch only from the CIMD host list, through Moodle's HTTP client with the site's blocked-hosts rules, with a short timeout and a size limit.
- [Faculty Moodles cannot add the root rewrite, and Copilot needs it] → Plugin-served discovery covers Claude if the day-1 test confirms it; for Copilot the admin self-check prints the one-line rule for the web server team.
- [The sandbox's Cloudflare in front of Moodle blocks Anthropic or Microsoft requests] → Either a Cloudflare skip rule for `160.79.104.0/21` and the Microsoft ranges on `/local/nitro/*` and `/.well-known/*`, or serve the sandbox directly on its fixed IP with its own certificate. Decided on day 1 (2026-09-19): keep the Cloudflare Tunnel and add no skip rule. Non-browser requests from outside (GET, JSON POST and form POST, with `python-httpx`, `node` and empty user agents, on `/login/`, `/.well-known/` and `/local/nitro/`) reach Moodle with no challenge and no `cf-mitigated` header. The tunnel's API token (in `~/.cloudflared/cert.pem` on `sbc`) cannot read or change zone security settings (403), so a skip rule, if ever needed, has to be added in the dashboard by the zone owner. Requests from Anthropic's range are confirmed by the probe (1.3); if they are challenged there, add the skip rule then. Direct HTTPS on a public IP is not an option without opening ports on the home network, and brings no benefit.
- [Copilot DCR connectors are reported to fail silently after sideloading (Microsoft Q&A, 2026)] → Copilot support is presented as standards-based and verified in the pilot; a hand-registered client is the fallback.
- [Upsert overwrites a teacher's manual edits in Moodle] → The result says `updated` and lists the changed fields; `dry_run` shows them first. The repository stays the source of truth, as in the teacher's Canvas workflow.
- [Sandbox signup depends on outbound email] → Configure and test SMTP on the sandbox on day 1; fallback: admin-created accounts from the demo signup list.
- [Many attendees sign up at once after the demo] → Course creation runs as an ad hoc task, so signup stays fast; check the time for 30 signups in a row on the sandbox.
- [Course and student data lands in the AI tool's conversation history, outside Moodle's reach] → The consent screen says so before the teacher grants anything; default reads leave out identity fields and submission content; per-tool control lets an admin narrow what is offered at all. The data-flow description, the threat model and the independent code review an institution will ask for are `nitro-pilot`.
- [Attendees put real student data on the sandbox] → Consent screen and login page notice; fictitious students only; sandbox data can be wiped at any time.
- [Without skills, the AI may chain quiz tools wrongly] → Rich `instructions` and tool descriptions; coarse, forgiving tools (category created on import, clear errors that say what to do next); run the demo script at least five times before 2026-10-02.
- [Internal quiz APIs differ between 5.0 and 5.1] → Pin the sandbox version for the demo, check the calls on that version, add version checks.
- [No Microsoft 365 tenant with Copilot licences] → The demo runs on Claude, Cowork is shown as a screen recording.
- [A live demo depends on network and AI latency] → Prepared course in the sandbox, recorded fallback of the full script.

## Attendee path (2026-09-20, tasks 4.5, 11.4)

The first real signup on the sandbox created its demo course through the onboarding flow, and Claude connected to it with the MCP URL alone: CIMD, Moodle login, consent, `initialize`, `tools/list` and tool calls all worked. The weekly loop was verified over HTTPS end to end (course, overview, missing submissions, message preview, confirmed send to exactly those four students; the fictitious students got the Moodle message and no email).

Three things went wrong on the way, all fixed:

- **The tool allowlist is stored at install time**, so tools added in a later version (`list_forum_posts`, `send_feedback`) were not listed. `db/upgrade.php` now adds new tools to the stored allowlist on upgrade, keeping the admin's own choices.
- **"Forgotten password" pointed at the old llm-kurzus GitHub discussions** (`forgottenpasswordurl` left over from the sandbox's earlier purpose), which is exactly where a stuck attendee lands. Cleared, so Moodle's own reset page is used.
- **The signup confirmation step blocked the attendee**: the confirmation email arrived and was opened, but the next step is easy to miss, and logging in before confirming just fails. The login page now says that the link in the email must be clicked. Task 11.6 (the attendee path without admin help) is therefore still open and needs a fresh account in a clean browser.

A wrong `cmid` for `list_forum_posts` now produces an error that names the course's forums and their course module IDs, so the AI can correct itself.

## Sandbox capacity (2026-09-20, task 10.6)

30 signups in a row on the sandbox, three runs. The first run created only 15 of 30 courses: a restore renames the new course to the template's name, which frees the short name the task had just reserved, and a failed run also rolled back the counter, so later tasks reused numbers and collided on the course short name. Fixed by taking the number from the courses that exist (inside the lock), renaming the course after the restore and skipping a taken number, giving the fictitious students course-id based usernames, and deleting a half-built course when provisioning fails so a retry starts clean. After the fix: 30 of 30, about 1.5 s of work per course.

Latency: the sandbox cron now runs every 20 s (was 60 s), so one signup has its course within about 22 s. A burst of 30 simultaneous signups takes about 2 minutes for the last course, because the queue is worked through sequentially. No email leaves for the fictitious students: they are `auth=nologin` with `@example.invalid` addresses, and Moodle's `email_to_user()` skips both.

## Security review (2026-09-20, task 11.3)

An independent review of the OAuth endpoints, the MCP endpoint and the confirmation protocol against the oauth-server spec, OAuth 2.1 / RFC 9700, RFC 7591, 8414, 9728, 9207 and the MCP authorization spec found no critical or high issues. Redirect URI matching (exact, port-agnostic loopback only, no userinfo or fragments), PKCE (S256 only, no downgrade), code binding, hashed tokens, refresh rotation, client authentication, consent CSRF, the connections page and the kill switch were found sound.

Fixed (regression tests in `local/nitro/tests/security_review_test.php`):

- Separate groups mode: `list_participants`, `list_submissions`, `grade_submission` and `message_students` now restrict a user without `moodle/site:accessallgroups` to their own groups, as the web UI does (`access::visible_users()`).
- Registration: bodies over 16 KB are refused (413) and a client may register at most 10 redirect URIs.
- CIMD: client ID URLs with a query string are refused, and the token endpoint never fetches a document, so unauthenticated requests cannot make the site send requests to claude.ai. Cache-only lookup alone turned out to break every refresh after a cache purge (each deploy purges caches), which threw Claude out with a 401 and a re-authorisation prompt; a client ID that already holds an authorization code or a grant is now accepted as the public client it is, without fetching or caching anything.
- Single use: codes, refresh tokens and confirmation tokens are claimed under a lock, so two parallel requests cannot both succeed.
- The consent screen's loopback warning follows the redirect URI actually presented; dynamically registered clients are labelled as self-registered and unverified; the consent page sends `frame-ancestors 'none'` and `X-Frame-Options: DENY` whatever the site's embedding setting.
- Tokens stop working when the user's auth method is disabled and during maintenance mode; a password change or account deletion revokes all the user's connections.
- A `message_students` confirmation covers exactly the recipients shown in the preview.
- The kill switch is enforced inside the tools too, so it also holds if an admin exposes them as a web service.
- The token endpoint refuses a client that authenticates both with HTTP Basic and in the body.

Accepted for the demo, revisit in the pilot:

- Never-used dynamic clients count against the cap for 7 days (the spec's clean-up period), so 1000 registrations can block new dynamic registrations for that long. The redirect allowlist means they cannot obtain codes, Claude uses CIMD and is unaffected, and an admin can delete clients on the clients page. A bulk "delete never-used clients" action is a pilot item.
- Refresh reuse detection has no grace period: a client that refreshes twice in parallel loses its connection and must reconnect. A replayed authorization code is refused but does not revoke tokens already issued from it (the code row is deleted on first use).
- Confirmation tokens are bound to user, tool and arguments, not to the OAuth grant.
- Turning the kill switch off and on again revives unexpired tokens; revoke connections on the clients page if needed.
- Forced password change and unaccepted site policy are not checked on MCP calls.
- `teams.microsoft.com` is shared by every tenant's agents, so the redirect host on the consent screen does not identify a Copilot client; consent phishing through a legitimate client cannot be prevented by the server beyond the consent wording.
- No RFC 7009 revocation endpoint (users revoke on their connections page) and no `Origin` check on `mcp.php` (see D2).

## Migration Plan

New plugin, no migration. On the sandbox: copy `local/nitro` and `local/nitrosandbox`, run the upgrade, enable email self-registration, keep the default redirect and CIMD allowlists, set the consent notice, upload the template course, and add the root well-known rewrite. Rotate the sandbox admin credentials before the demo. Rollback: disable with the kill switch or uninstall the plugin, which drops its tables and invalidates all tokens.

## Open Questions

- Exact token lifetimes and the never-used client cap (the specs set upper bounds and behaviour).
- ~~Whether the Cowork manifest is valid with only `agentConnectors` and no skill.~~ Resolved 2026-09-20: Microsoft's Cowork plugin guide (2026-09-17) lists "connector only (no custom skills)" as a supported package; each connector must ship an `mcpToolDescription` file (the `tools/list` result). Cowork also reads MCP tool annotations (`readOnlyHint`, `destructiveHint`, `title`) to decide its own confirmation prompts. Package builder and pilot steps: `copilot/`.
