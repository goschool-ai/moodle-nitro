---
id: UC-01m4v5swwpyn1vtbq5zkjbkcv9
form: use-case
title: "Connect an AI assistant to Moodle"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
  - A-01m4v5svqwf7p6qmqf4gj4dmhq
goal:
  - G-01m4v5j7bt6gscpre5njg4hmw3
refines:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
  - BR-01m4v5j7bt9ahkexfas7g324tf
  - BR-01m4v5j7bt8ewybvncggz1prdn
  - BR-01m4v5j7btjrh80q3a7z21xadp
  - BR-01m4v5j7btw45p8ys1rk5wpj5r
  - BR-01m4v5j7btkhbb5ah08j7f5h4y
  - BR-01m4v5j7bts0wwhsmadh9fjtda
  - BR-01m4v5j7btvf34sed2vnnvhe97
  - BR-01m4v5j7btfvmc36fr4thmn9pm
  - BR-01m4v5j7btgx9v6ccgw8azb6hg
  - BR-01m4v6116fwy56rj9b8tcg6hg0
  - BR-01m4v611jskt85yswm2rv7x4r5
  - BR-01m4v610tbvbp1m7df2ywz0gp4
  - BR-01m4v611cemfb81e81333p2bdv
  - BR-01m4v610mdsezkxa7hdvk3h1g2
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3a. What Claude actually does"
    - "PRODUCT.md · Product Purpose"
  quote: "Success means a teacher connects by pasting one URL and signing in, sees what they are allowing, and can take it back at any time."
  inferred: null
---
# Connect an AI assistant to Moodle

## Intent

The teacher connects their AI assistant to Moodle by pasting one URL and signing in with their Moodle account, and sees what they allow before they allow it.

## Preconditions

Nitro is switched on, the teacher holds `local/nitro:use` somewhere, and the client's redirect URI is on the allowlist.

## Main success scenario

1. The teacher adds `https://<moodle>/local/nitro/mcp.php` as a custom connector in their AI assistant.
2. The assistant calls the MCP URL, gets 401 and discovers the authorization server from the metadata.
3. The assistant identifies itself (a Client ID Metadata Document for Claude) and starts the authorization code flow with PKCE.
4. Moodle asks the teacher to log in through the site's normal login (including SSO).
5. The consent screen shows the client's name, its redirect host, what it can reach and any notice the admin set; the teacher approves.
6. The assistant exchanges the code for an access and a refresh token and calls `initialize` and `tools/list`.

## Alternatives

- The client registers dynamically (Copilot) or was registered by an admin.
- The teacher has no `local/nitro:use`: the consent screen says they are not permitted.
- The redirect URI is not allowed: the flow stops on a Moodle page that names it.
- The access token expires: the assistant refreshes it with rotation, without the teacher.
