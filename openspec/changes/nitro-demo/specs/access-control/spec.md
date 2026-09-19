## Purpose

Guarantees that exactly as much is possible through the AI as through the Moodle web UI, no more and no less, and lets admins limit and switch off AI access with the tools they already use.

## ADDED Requirements

### Requirement: Access gate capability
The plugin SHALL define one capability, `local/nitro:use`, which is only an entry gate: it grants no permission beyond what the user's other Moodle capabilities already allow. The plugin SHALL NOT create roles or change existing role definitions. Admins SHALL be able to grant it at system, category or course level through normal role management.

#### Scenario: Gate granted in one course only
- **WHEN** a teacher holds `local/nitro:use` only in course A and calls a tool on course B where they are also a teacher
- **THEN** the call fails with a permission error and nothing is changed in course B

#### Scenario: Gate does not add power
- **WHEN** a user holds `local/nitro:use` but lacks `moodle/course:manageactivities` in a course
- **THEN** content-creating tools fail in that course exactly as the web UI would forbid it

### Requirement: Same capabilities as the web UI
Every tool MUST validate the target context (course or module) and check the Moodle capabilities the equivalent web UI action requires, before reading or changing anything.

#### Scenario: Student token
- **WHEN** a user enrolled only as a student calls `list_submissions` for an assignment
- **THEN** the call fails with a permission error and no submission data is returned

### Requirement: Data minimisation
Read tools SHALL return only the fields needed for course work by default. Email addresses and identity fields (such as a student ID in `idnumber`) SHALL be returned only when the call explicitly asks for them and the user is allowed to see them in the web UI.

#### Scenario: Default participant list
- **WHEN** a teacher calls `list_participants` without asking for identity fields
- **THEN** the result contains user ID, full name, roles and groups, and no email address or ID number

#### Scenario: Explicit identity request
- **WHEN** a teacher asks for email addresses and has permission to view them in the participants page
- **THEN** the result includes email addresses

### Requirement: Site kill switch
A site-level setting SHALL enable or disable the plugin. While disabled, the MCP endpoint and the OAuth endpoints MUST refuse all requests and existing tokens MUST NOT work.

#### Scenario: Admin disables the plugin
- **WHEN** the admin turns the setting off
- **THEN** every MCP request is answered with HTTP 503 and every OAuth request is refused, until the setting is turned on again

### Requirement: Consent screen notice
The admin SHALL be able to set a notice text shown on the OAuth consent screen, for example to state that a site is a sandbox and must not hold real student data.

#### Scenario: Sandbox notice
- **WHEN** the admin has set a notice and a user opens the consent screen
- **THEN** the notice is shown above the approve button

### Requirement: Hungarian and English
Every user-facing string of the plugin (consent screen, connections page, settings, errors, tool error messages) SHALL be available in Hungarian and English, and SHALL follow the user's Moodle language.

#### Scenario: Hungarian teacher
- **WHEN** a teacher whose Moodle language is Hungarian opens the consent screen
- **THEN** every text on it, including the admin-independent explanations, is in Hungarian

### Requirement: Discovery self-check
The plugin's settings page SHALL fetch the site's own discovery URLs (the plugin-served ones and the root `/.well-known/` ones) and show which respond correctly. When the root URLs do not respond, it SHALL show the web server rewrite rule that would enable them, and state that it is optional.

#### Scenario: Site without rewrite
- **WHEN** an admin opens the settings page on a site without the rewrite rule
- **THEN** the plugin-served discovery URLs are shown as working, the root URLs as missing, and the rewrite rule for the detected web server is shown as optional

### Requirement: No third-party data flow
The plugin MUST NOT send course or user data anywhere other than back to the requesting client in the response, and MUST NOT store student data outside Moodle. The only outbound requests the plugin makes are fetches of client metadata documents from allowed hosts, which carry no course or user data.

#### Scenario: Tool call makes no outbound request
- **WHEN** any tool is called
- **THEN** the plugin makes no outbound network request

#### Scenario: Client metadata fetch
- **WHEN** the authorization server fetches a client metadata document
- **THEN** the request goes only to an allowed CIMD host and contains no user or course data
