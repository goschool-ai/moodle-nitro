---
id: EX-01m4v5z5x00cxn9g9m833rgja4
form: example
title: Fresh install answers nothing
subjects:
  - BR-01m4v5z5gtqc7gaajtjvg31w5p
  - UC-01m4v5swm98nywm4eb9ez2hth6
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/settings.php · local_nitro/active (default 0)"
    - "local/nitro/classes/mcp/server.php · server::handle"
    - "local/nitro/classes/admin/setting_selfcheck.php · output_html"
  inferred: "Read from the setting's default and from what the endpoints and the settings page do while nitro is off."
---
# Fresh install answers nothing

## Given

local_nitro has just been installed, and nobody has changed its settings.

## When

an AI client sends a request to the MCP endpoint, and the admin opens nitro's settings page

## Then

the MCP endpoint answers HTTP 503, and the settings page asks the admin to switch nitro on before the discovery check can run
