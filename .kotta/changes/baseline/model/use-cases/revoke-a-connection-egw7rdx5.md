---
id: UC-01m4v5sx33f7734ss0egw7rdx5
form: use-case
title: "Revoke a connection"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7bt6gscpre5njg4hmw3
refines:
  - BR-01m4v5j7bt7gnewzbmexq090x9
  - BR-01m4v610earfshm4bfz7xmnxc6
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3. OAuth 2.1 server"
    - "local/nitro/README.md · 6. Connecting an AI client"
  quote: "The profile page /local/nitro/connections.php lists grants (user + client) and revokes them by deleting all their tokens."
  inferred: null
---
# Revoke a connection

## Intent

The teacher sees which AI tools are connected to their Moodle account and takes access back from any of them.

## Preconditions

The teacher has connected at least one AI assistant.

## Main success scenario

1. The teacher opens Connected AI tools from their Moodle profile.
2. They see each connection with the client's name, redirect host and last use.
3. They revoke one; all its tokens stop working at once.

## Alternatives

- A password change or account deletion revokes all the teacher's connections without this page.
