---
id: UC-01m4v5szsa3p32es45jh0e8rvq
form: use-case
title: "Register an AI client by hand"
level: user-goal
actor:
  - A-01m4v5svz9q5s0fwj4xnjp14j4
goal:
  - G-01m4v5j7bt6gscpre5njg4hmw3
refines:
  - BR-01m4v5j7btm6qyqm6d798d1zpa
  - BR-01m4v6110c7bjb2ac1t42wt5a1
extends:
  - UC-01m4v5sz8xw7h30c3nba92b51n
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 7. Administration"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3. OAuth 2.1 server"
  quote: "Register a client yourself here, as confidential (it gets a secret, shown once) or public, or delete one."
  inferred: null
---
# Register an AI client by hand

## Intent

The administrator registers an AI client by hand, for sites that switch dynamic registration off or for clients that need a secret.

## Preconditions

Nitro is installed; the client's redirect URIs are on the allowlist.

## Main success scenario

1. On the OAuth clients page the administrator registers a client with a name and redirect URIs, as confidential or public.
2. A confidential client's secret is shown once.
3. The client connects with that client ID.

## Alternatives

- A redirect URI is not on the allowlist: registration is refused.
- The administrator deletes a client: its connections stop working.
