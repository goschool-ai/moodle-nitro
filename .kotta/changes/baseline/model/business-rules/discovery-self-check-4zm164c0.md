---
id: BR-01m4v5j7btp3h0hp8a4zm164c0
form: business-rule
title: Discovery self-check
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-proposed-human-approved
  sources:
    - ".kotta/changes/baseline/conversation.md · J1"
    - 'openspec/specs/access-control/spec.md · Requirement: Discovery self-check'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context (admins cannot change the web server); D4; D3a; Risks (Copilot needs the root rewrite)"
    - "local/nitro/README.md · 3. Web server (Root discovery URLs, required for Claude)"
    - "local/nitro/classes/local/selfcheck.php · selfcheck::run, selfcheck::rewrite_rules"
    - "local/nitro/classes/admin/setting_selfcheck.php · output_html"
  quote: >-
    The plugin's settings page SHALL fetch the site's own discovery URLs (the
    plugin-served ones and the root `/.well-known/` ones) and show which respond
    correctly.
  inferred: "Scope: that the check runs only while nitro is switched on and shows the rule for the detected web server first, read from setting_selfcheck.php."
---
# Discovery self-check

## Rule

The plugin's settings page SHALL fetch the site's own discovery URLs (the plugin-served ones and the root `/.well-known/` ones) and show which respond correctly. When the root URLs do not respond, it SHALL show the web server rewrite rule that would enable them, and state that Claude and Microsoft 365 Copilot need it.

## Rationale

Faculty Moodle admins can install plugins but often cannot change the web server configuration (design, Context). Plugin-served discovery needs no web server change, but Claude looks for the authorization server metadata only at the root URL (design D3a), so the root rewrite is required for Claude and Copilot; the self-check tells the admin whether it is in place and gives the rule to hand to the web server team (design D4 and Risks).

## Scope

The Discovery check on nitro's settings page. It runs only while nitro is switched on, fetches two plugin-served and two root URLs, and shows rules for an Apache virtual host, `.htaccess` and nginx, with the detected server's rule first. It does not change the web server.
