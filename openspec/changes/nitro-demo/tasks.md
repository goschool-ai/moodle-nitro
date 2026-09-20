## 1. Day-1 checks (unblock the skeleton)

- [x] 1.1 Confirm the sandbox's Moodle version: 5.2.2 with the `public/` web root (from the llm-kurzus sessions); PHP 8.3 (response headers)
- [ ] 1.2 Decide how the sandbox is exposed: a Cloudflare skip rule for Anthropic (`160.79.104.0/21`) and Microsoft on `/local/nitro/*` and `/.well-known/*`, or direct HTTPS on the fixed IP with its own certificate; verify a request from outside reaches Moodle without a challenge, and record the choice in design.md
- [ ] 1.3 Discovery probe: a throwaway PHP script in `/nitroprobe/` on the sandbox that logs every request and plays through PRM, AS metadata, CIMD/DCR, authorize, token and a one-tool MCP server, in variants V0 (root well-known), V1 (path-appended OpenID form with OpenID fields) and V2 (without them); run it with Claude web, Claude Code and MCP Inspector; record which URLs each client fetched, the redirect URIs, the `resource` and `scope` values, and whether CIMD was used, in design.md (D3, D4); delete the probe afterwards
- [ ] 1.4 Read `webservice_mcp` for schema generation and JSON-RPC edge cases, and check existing Moodle OAuth server plugins; note what is reused in design.md (D2, D3)
- [ ] 1.5 Set up a local Moodle 5.2 dev site with PHPUnit and Behat initialised; verify `vendor/bin/phpunit --testsuite local_nitro_testsuite` runs (empty)
- [ ] 1.6 Configure outbound email on the sandbox (it currently has `noemailever`) and enable email self-registration; verify a test signup receives the confirmation email
- [ ] 1.7 Rotate the sandbox admin password and web service token; verify the old credentials no longer work

## 2. Plugin skeleton

- [ ] 2.1 Create `local/nitro` with `version.php`, `lang/en/local_nitro.php` and `lang/hu/local_nitro.php` (kept complete in both languages from the first string), `settings.php` (enable switch, tool allowlist, redirect URI allowlist and CIMD host list with defaults, DCR switch, never-used client cap, consent notice); verify the plugin installs on the dev site
- [ ] 2.2 Define `local/nitro:use` in `db/access.php` without default role archetypes; verify it shows in role definitions
- [ ] 2.3 Add `db/install.xml` tables for clients, codes, tokens and confirmations; verify install and uninstall on the dev site
- [ ] 2.4 Add the `tool_called` event class; verify with a PHPUnit test that it appears in the log store without content fields
- [ ] 2.5 `classes/privacy/provider.php` declaring the plugin's tables with their fields and purpose and the transmission of course and user data to the connected client, with export and deletion; verify with PHPUnit that the metadata is complete and that deleting a user removes their tokens, codes and pending confirmations

## 3. OAuth server

- [ ] 3.1 `metadata.php` serving protected resource metadata and authorization server metadata (scopes, auth methods, CIMD flag), including the slash-argument `/.well-known/openid-configuration` form; verify with PHPUnit and with `curl` on the dev site with and without the root rewrite
- [ ] 3.2 Redirect URI allowlist matching with port-agnostic loopback; verify the allowlist scenarios with PHPUnit
- [ ] 3.3 CIMD: fetch, validate and cache client metadata documents from the CIMD host list through Moodle's HTTP client; verify the CIMD scenarios with PHPUnit using a mocked HTTP response
- [ ] 3.4 Dynamic client registration for public and confidential clients (secret issued and hashed), never-used cap and DCR switch; verify each registration scenario with PHPUnit
- [ ] 3.5 Admin page to register, list and delete clients by hand; verify with a Behat scenario
- [ ] 3.6 Scheduled task deleting dynamic clients without a token after 7 days; verify with PHPUnit
- [ ] 3.7 Authorization endpoint with `require_login()`, client and redirect URI validation, PKCE S256, `resource` check, `local/nitro:use` check and consent screen (client name, redirect host, loopback warning, what the client can read and that it reaches the provider and may stay in the conversation history, the separate approval for writes, admin notice); verify each authorization scenario with PHPUnit or Behat
- [ ] 3.8 Token endpoint (form-urlencoded) for code exchange with client authentication for confidential clients, and refresh rotation with reuse detection; verify each token scenario with PHPUnit
- [ ] 3.9 Bearer token validation helper that rejects expired, revoked, suspended-user and disabled-plugin cases; verify with PHPUnit
- [ ] 3.10 Connections page in the user profile listing grants with last use, and revoking them; verify the revocation scenario with Behat
- [ ] 3.11 Discovery self-check on the settings page with the rewrite rule for Apache and nginx; verify on the dev site with and without the rule

## 4. MCP endpoint

- [ ] 4.1 `mcp.php` JSON-RPC handling: initialize (with `instructions`), notifications (202), unknown methods (-32601), GET (405), 401 with `resource_metadata`; verify with PHPUnit request tests
- [ ] 4.2 Tool registry from the allowlist with JSON Schema generated from external function parameter descriptions; verify `tools/list` output against a schema snapshot test
- [ ] 4.3 `tools/call` dispatch as the token user, argument validation, `isError` mapping for exceptions and capability failures, event triggering; verify with PHPUnit
- [ ] 4.4 Course-level `local/nitro:use` check for every tool that targets a course; verify the "gate granted in one course only" scenario
- [ ] 4.5 Deploy to the sandbox and connect Claude by pasting only the MCP URL; verify CIMD, login, consent, `initialize` and `tools/list` succeed
- [ ] 4.6 Per-tool enabling from the settings allowlist; verify with PHPUnit that a disabled tool is absent from `tools/list` and refused on call, that the others keep working, and that unticking every write tool leaves a read-only site

## 5. Read tools (skeleton milestone, 2026-09-25)

- [ ] 5.1 `list_courses` including hidden courses; verify the teacher/student and hidden-course scenarios with PHPUnit
- [ ] 5.2 `course_overview` with sections, activities (with keys), due dates, hidden flags and student count; verify with PHPUnit on a generated course
- [ ] 5.3 `list_participants` with role/group filter, last course access, never-accessed filter and identity-field opt-in; verify the scenarios
- [ ] 5.4 `list_submissions` with missing/late/ungraded/graded filters, extensions, grading state and `include_content` (online text, links, file names); verify with PHPUnit using assign generators, including that graded students stay in the list
- [ ] 5.5 Skeleton check: Claude on the sandbox lists courses, opens an overview and lists missing submissions from a single prompt

## 6. Write safety

- [ ] 6.1 `dry_run` support shared by all write tools, returning effective settings; verify a dry run changes nothing
- [ ] 6.2 Confirmation token service (issue, argument hash, single use, 10-minute expiry, user binding); verify each write-confirmation scenario with PHPUnit

## 7. Content tools

- [ ] 7.1 Shared markdown-to-HTML with the cleaning report, and ISO 8601 date handling with weekday echo; verify conversion, removed-element reporting and time zone scenarios
- [ ] 7.2 Key lookup by course module `idnumber` and partial update overlay on `get_moduleinfo_data()`; verify create, update and description-only update with PHPUnit
- [ ] 7.3 `save_page`; verify the first-publish, republish and nonexistent-section scenarios
- [ ] 7.4 `save_assignment` with mandatory submission types and grading on create, due and cut-off dates, and echo of effective settings; verify the scenarios and that the due date shows in the calendar

## 8. Quiz tools

- [ ] 8.1 `import_questions` for GIFT and Moodle XML into shared or quiz bank, with category creation and all-or-nothing errors; verify the scenarios with PHPUnit
- [ ] 8.2 `create_quiz` with timing, attempts, shuffle and review presets; verify the settings in the created quiz
- [ ] 8.3 `add_questions_to_quiz` for fixed and random questions, marks and paging, with the not-enough and already-attempted errors; verify with PHPUnit
- [ ] 8.4 Version guard for Moodle < 5.0; verify the error message

## 9. Student-facing tools

- [ ] 9.1 `message_students` by user IDs, group or all students, one-to-one, through the confirmation protocol, reporting undelivered recipients; verify the scenarios with PHPUnit and the message sink
- [ ] 9.2 `post_announcement` to the news forum (created if missing), with subscriber count and hidden-course warning in the preview; verify with PHPUnit and the message sink
- [ ] 9.3 `grade_submission` for points and scale items with optional feedback comment, following marking workflow and notification settings, through the confirmation protocol; verify the scenarios with PHPUnit and the gradebook

## 10. Sandbox onboarding

- [ ] 10.1 Build the template demo course (week-4 material, assignment with soft and hard deadline, empty shared question bank) and export it as a backup without user data; verify it restores cleanly
- [ ] 10.2 Create `local/nitrosandbox` with the signup observer and the ad hoc task: restore as "Demo NN", enrol the user as editing teacher, grant `local/nitro:use`; verify with PHPUnit that `local_nitro` works without it installed
- [ ] 10.3 Generate 20 fictitious students per course (`nologin`, unroutable email, two groups) and on-time, late and missing submissions (online text with a repository link); verify the isolation and submission scenarios with PHPUnit
- [ ] 10.4 Set the sandbox consent notice and login page notice; verify both are visible
- [ ] 10.5 Run 30 signups in a row on the sandbox; verify every course is ready within one minute and no email leaves for fictitious students

## 11. Clients, review and demo (demo-ready milestone, 2026-10-02)

- [ ] 11.1 Write the server `instructions` and every tool description, and review them against the demo script (weekly loop: who is behind, reminder, accept, announce; then the quiz); verify Claude completes both without extra hints
- [ ] 11.2 Cowork connector manifest (connector only) prepared and validated with Agents Toolkit if a tenant is available; otherwise document the Copilot steps for the pilot
- [ ] 11.3 Security review of the OAuth endpoints (authorize, token, register, metadata, CIMD fetch, connections) against the oauth-server spec and the OAuth 2.1 security considerations; verify all findings are fixed or recorded in design.md
- [ ] 11.4 Prepare the stage demo course on the sandbox through the onboarding flow itself; verify it is created by a real signup
- [ ] 11.5 Run the full demo script five times on the sandbox; verify three consecutive clean runs, and record one run as a fallback video
- [ ] 11.6 Walk through the attendee path as a new user (signup, connect Claude by URL, first prompt) on a clean browser; verify it works without admin help
- [ ] 11.7 Needs-list form for the demo, including "Which AI tool do you have now: M365 Copilot, only Copilot Chat, Claude, none?"; verify it is ready and linked from the last slide
- [ ] 11.8 Run the full PHPUnit and Behat suites and Moodle code checker; verify they pass with no errors
