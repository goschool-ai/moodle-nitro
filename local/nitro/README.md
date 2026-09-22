# nitro for Moodle – installation guide

nitro (`local_nitro`) lets a teacher work in Moodle from an AI assistant (Claude, Microsoft 365 Copilot, or another MCP client) with exactly the permissions they have in Moodle. The plugin is an MCP server and has its own OAuth 2.1 authorization server, so no external identity service is involved.

This guide is for Moodle site administrators. It takes about 30 minutes, most of it on the web server rule.

> `local_nitrosandbox`, in the same repository, only builds demo courses for the GoSchool practice site. Do not install it on an institutional Moodle.

## 1. Requirements

- Moodle 5.0 or later. The quiz tools use the Moodle 5.0 question banks and refuse to run on older versions.
- HTTPS on the Moodle site. AI clients reject plain HTTP.
- Cron running at least every few minutes. It cleans up expired tokens and retries feedback mail.
- Outgoing mail, if you keep the feedback tool on (section 4).
- Access to the web server configuration, for the rule in section 3.

## 2. Install the plugin

1. Copy the `nitro` folder to `local/nitro` in your Moodle code. On Moodle 5.1 and later, where the code lives under `public/`, use `public/local/nitro`.
2. Finish the installation: open *Site administration → Notifications*, or run
   ```
   php admin/cli/upgrade.php --non-interactive
   ```
3. nitro is installed switched **off**. Nothing answers until you switch it on in section 4.

## 3. Web server: discovery URLs and the Authorization header

### Root discovery URLs (required for Claude)

AI clients look for the OAuth metadata at the site root, for example `/.well-known/oauth-authorization-server/local/nitro/oauth/metadata.php`, outside Moodle's own paths. Claude does this; Microsoft 365 Copilot does the same. Route these root URLs to nitro.

**Apache**: add this to the `.htaccess` in the web root (the `public/` folder on Moodle 5.1+). `AllowOverride` must permit it.

```apache
# nitro: root OAuth discovery URLs, served by local_nitro.
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^\.well-known/oauth-protected-resource(/.*)?$ /local/nitro/oauth/metadata.php/.well-known/oauth-protected-resource [END]
    RewriteRule ^\.well-known/(oauth-authorization-server|openid-configuration)(/.*)?$ /local/nitro/oauth/metadata.php/.well-known/$1 [END]
</IfModule>
```

**nginx**: add this inside the Moodle `server` block, before the PHP `location`:

```nginx
# nitro: root OAuth discovery URLs, served by local_nitro.
location ~ ^/\.well-known/oauth-protected-resource(/.*)?$ {
    rewrite ^ /local/nitro/oauth/metadata.php/.well-known/oauth-protected-resource last;
}
location ~ ^/\.well-known/(oauth-authorization-server|openid-configuration)(/.*)?$ {
    rewrite ^/\.well-known/([^/]+) /local/nitro/oauth/metadata.php/.well-known/$1 last;
}
```

These nginx rules follow the Apache ones but have not been run against a live nginx site yet; the Discovery check shows whether they work. Moodle's usual nginx setup already passes the path after `metadata.php` to PHP (slash arguments). If other Moodle URLs such as `pluginfile.php/...` work, this works too.

### The Authorization header

Every MCP request carries `Authorization: Bearer …`. Some PHP-FPM and CGI setups drop this header before it reaches PHP. The symptom is an AI client that logs in successfully and then keeps getting "unauthorized".

- Apache with PHP-FPM or CGI: add `CGIPassAuth On` to the Moodle directory configuration.
- nginx: add `fastcgi_param HTTP_AUTHORIZATION $http_authorization;` to the PHP `location`.

### Check

In nitro's settings (section 4), the **Discovery check** at the top fetches every discovery URL from the site itself and shows which ones answer. All lines should be green before you connect a client.

## 4. Settings

*Site administration → Plugins → Local plugins → nitro → Settings*

| Setting | What to do |
|---|---|
| **Discovery check** | Read it first; see section 3. |
| **Enable nitro** | Switch on when you are ready. Switching it off is the kill switch: the MCP and OAuth endpoints refuse every request and existing tokens stop working immediately. |
| **Allowed tools** | All tools are on by default. Untick the ones you do not want AI clients to see or call. Read-only tools: `list_courses`, `course_overview`, `list_participants`, `list_submissions`, `list_forum_posts`, `read_activity`. Anything that reaches students (`message_students`, `post_announcement`, `grade_submission`) always needs the teacher's explicit approval of a preview. |
| **Allowed redirect URIs** | Where nitro may send a user back after login, one per line. The default covers Claude, Microsoft 365 Copilot and local desktop clients (`localhost` and `127.0.0.1` match any port). Add a client's callback address here before it can connect (section 6). Every client is held to this list, however it registers. |
| **Client metadata document hosts** | Hosts from which clients may identify themselves with a metadata document. Default: `claude.ai`. |
| **Dynamic client registration** | On by default; Microsoft 365 Copilot and some other clients need it. If you switch it off, only clients you register yourself (section 7) and clients with a metadata document, such as Claude, can connect. |
| **Limit of never-used clients** | Limits unused self-registered clients (default 1000). Unused clients are deleted after 7 days. |
| **Let teachers send feedback**, **Feedback address** | The `send_feedback` tool mails what a teacher found missing or broken to the address set here (default: the nitro team at GoSchool, `info@goschool.ai`). The mail carries only the teacher's text, the site name and URL and version numbers, never course or student data, and the teacher approves the exact text first. Put your own address here to keep feedback inside the institution, or switch the tool off. |
| **Consent screen notice** | Optional text shown on the approval screen, for example your institution's rule on personal data in AI tools. |

## 5. Who may use it

nitro adds one capability, `local/nitro:use`. It is only an entry gate, and **no role has it by default**. Every tool also checks the capability the same action needs in the Moodle web interface, so a teacher can do through the AI exactly what they can do in the browser, and no more.

Typical choices:

- **Pilot with a few teachers:** create a role "nitro user" with `local/nitro:use` allowed (context: course), and assign it to those teachers in their courses or in a category.
- **All teachers of a faculty:** allow `local/nitro:use` for the Teacher (`editingteacher`) role, overriding it at the faculty's category.

The capability carries personal data, spam and data loss risks, because the AI can read participants and submissions, message students and change content, always on the teacher's behalf and with their approval.

## 6. Connecting an AI client

The teacher needs the MCP address of your site:

```
https://<your-moodle>/local/nitro/mcp.php
```

- **Claude:** *Settings → Connectors → Add custom connector*, paste the address, then log in to Moodle and approve. Works with the default redirect URIs.
- **Microsoft 365 Copilot (Cowork):** needs a full Microsoft 365 Copilot licence; Cowork is not available with Copilot Chat alone. The connector package and steps are in `copilot/` in the repository.
- **Other clients**, for example the GoSchool assistant: add the client's callback address to **Allowed redirect URIs** first. For the GoSchool assistant this is:
  ```
  https://oktat-ai.web.app/api/mcp_oauth_callback
  ```
  Without it, the connection stops with "This site does not allow the redirect URI …".

Every connection ends on an approval screen in Moodle that names the client and the site it returns to. The teacher sees their connections on the **Connected AI tools** page, linked from their profile, and can revoke each one; revoking invalidates its tokens at once.

## 7. Administration

- **OAuth clients** (*Site administration → Plugins → Local plugins → nitro → OAuth clients*): lists registered clients. Register a client yourself here, as confidential (it gets a secret, shown once) or public, or delete one. Its redirect URIs must be on **Allowed redirect URIs**.
- **Log:** every tool call is logged as the event *AI tool called* (`\local_nitro\event\tool_called`) with user, course and tool, but without the content. Find it in the standard Moodle logs.
- **Privacy:** nitro implements the Moodle privacy API for its connections and tokens.

## 8. Upgrading

Replace the `local/nitro` folder and run the upgrade as in section 2. The upgrade adds new tools to **Allowed tools** so teachers get them without further steps. It adds back a tool you had removed, too, so check the list after each upgrade.

Upgrading purges Moodle's caches. Existing connections survive it; teachers do not have to reconnect.

## 9. Troubleshooting

| Symptom | Cause and fix |
|---|---|
| "This site does not allow the redirect URI …" | The client's callback is not in **Allowed redirect URIs**. Add it exactly as shown in the message. |
| The client logs in, then keeps saying unauthorized | The `Authorization` header does not reach PHP; see section 3. |
| The client cannot find the login or says discovery failed | The root discovery rule is missing; see the **Discovery check** in section 4. |
| The teacher sees "not permitted" on the approval screen | They do not have `local/nitro:use` anywhere; see section 5. |
| Every request is refused | **Enable nitro** is off. |
| Feedback is not delivered | Check outgoing mail and that cron runs. If the mail server was down, nitro queues the message and retries, so a single failure is not lost. |

## 10. Uninstalling

*Site administration → Plugins → Plugins overview → nitro → Uninstall*. This deletes all clients, connections and tokens. Remove the web server rule from section 3 afterwards.
