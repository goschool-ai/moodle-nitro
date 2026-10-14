---
id: BR-01m4v5z5gtqc7gaajtjvg31w5p
form: business-rule
title: nitro is installed switched off
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 2. Installation (installed switched off); 4. Settings (Enable nitro)"
    - "local/nitro/settings.php · local_nitro/active (default 0)"
    - "local/nitro/classes/admin/setting_selfcheck.php · output_html"
  inferred: "The obligation is read from the setting's default in settings.php. The reason comes from the admin guide's 'Switch on when you are ready'."
---
# nitro is installed switched off

## Rule

After installation, the site setting `Enable nitro` SHALL be off. Until an admin switches it on, the MCP endpoint and the OAuth endpoints MUST refuse every request.

## Rationale

The admin guide says nitro is installed switched off and nothing answers until the admin switches it on, when they are ready. Before that, the admin can set up the discovery rule, the allowed tools and redirect URIs, and decide who gets the gate.

## Scope

A fresh installation of local_nitro. After that, the switch behaves as the rule "Site kill switch" says. While nitro is off, the settings page skips the discovery check and asks the admin to switch nitro on first.
