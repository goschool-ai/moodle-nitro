# Connecting Microsoft 365 Copilot Cowork to nitro

Written 2026-09-20 from Microsoft's "Build plugins for Copilot Cowork" (updated 2026-09-17). Not yet tested against a
tenant: nitro-demo has no Microsoft 365 tenant with Copilot licences, so this is the plan for the pilot.

## What Copilot needs from the Moodle site

1. **nitro installed and switched on**, with `local/nitro:use` granted to the teachers who will connect.
2. **The Teams redirect URI on the allowlist.** `https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect` is there
   by default (Site administration > Plugins > Local plugins > nitro > Settings).
3. **The root discovery rewrite.** Microsoft documents the root `/.well-known/oauth-protected-resource` and
   `/.well-known/oauth-authorization-server` URLs, so expect Copilot to need them. The nitro settings page runs a
   discovery check and prints the one-line rule for Apache, `.htaccess` or nginx.
4. **The `Authorization` header reaching PHP** (`CGIPassAuth On` on Apache with PHP-FPM, or
   `fastcgi_param HTTP_AUTHORIZATION $http_authorization;` on nginx).

## The package

A connector-only Microsoft 365 app package (no skills: nitro carries its own guidance in the MCP server
instructions and tool descriptions). Build it for a site with:

```bash
python3 copilot/build.py --site https://moodle.example.edu --name "Example University"
```

It contains `manifest.json` (schema v1.28, one `agentConnectors` entry pointing at `<site>/local/nitro/mcp.php`),
`tools/nitro-tools.json` (the `mcpToolDescription` file: nitro's `tools/list`, taken from the PHPUnit snapshot) and
placeholder icons. Replace the icons with real ones before any store submission.

Cowork reads the MCP tool annotations: tools with `readOnlyHint: false` or `destructiveHint: true` get a confirmation
prompt in Cowork itself, on top of nitro's own server-side confirmation for messages, announcements and grades.

## Authentication: two ways

**A. Dynamic client registration (try first).** Build without `--reference-id`. Cowork registers an OAuth client with
nitro's registration endpoint (it asks for a client secret, which nitro issues) and each teacher signs in once through
the normal Moodle login. Microsoft Q&A reports (2026) that Cowork connectors with dynamic registration can silently
fail to activate after sideloading: no Connect prompt and no request to the server. Watch the web server log for
`POST /local/nitro/oauth/register.php`; if it never comes, use B.

**B. A client registered by hand (fallback).**

1. In Moodle, Site administration > Plugins > Local plugins > nitro > OAuth clients: register "Microsoft 365 Copilot"
   with the Teams redirect URI and "Issue a client secret" ticked. Copy the client ID and secret (shown once).
2. Register the client in the Microsoft Enterprise Token Store with Agents Toolkit (`atk`, the OAuth client
   registration step of "Configure OAuth 2.0 authentication" for Microsoft 365 Copilot): authorization URL
   `<site>/local/nitro/oauth/authorize.php`, token URL `<site>/local/nitro/oauth/token.php`, scope
   `nitro offline_access`, client ID and secret from step 1, usage **Any Microsoft 365 Organization**.
3. Build the package with `--reference-id <the registration ID>`.

## Install and test

```bash
npm install -g @microsoft/m365agentstoolkit-cli
atk auth login
atk install --file-path copilot/build/nitro-xxxxxxxx.zip --scope Personal
```

Then in Cowork: Sources & Skills > Plugins, connect, sign in to Moodle, approve the consent screen. First prompt:
"List my Moodle courses." For the whole organisation, upload the package in the Microsoft 365 admin center
(Manage apps > Upload custom app > Add agent).

## Sources

- Build plugins for Copilot Cowork: https://learn.microsoft.com/en-us/microsoft-365/copilot/cowork/cowork-plugin-development
- Configure dynamic client registration: https://learn.microsoft.com/en-us/microsoft-365/copilot/extensibility/plugin-authentication-dynamic-client-registration
- Configure OAuth 2.0 authentication: https://learn.microsoft.com/en-us/microsoft-365/copilot/extensibility/plugin-authentication-oauth
- Register MCP servers as agent connectors: https://learn.microsoft.com/en-us/microsoftteams/platform/m365-apps/agent-connectors
- Cowork DCR connector silently fails after sideload (Microsoft Q&A): https://learn.microsoft.com/en-au/answers/questions/5886163/cowork-(frontier)-plugin-connector-with-dynamiccli
