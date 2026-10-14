---
id: E-01m4v5t0xbzfhgcxr05wz1wxcs
form: entity
title: "OAuth client"
used_by:
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
  - UC-01m4v5szsa3p32es45jh0e8rvq
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/db/install.xml · local_nitro_client"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3. OAuth 2.1 server"
  quote: "OAuth clients registered dynamically or by an admin. CIMD clients are not stored here."
  inferred: null
---
# OAuth client

## Meaning

An AI client that may ask a teacher for access. Clients that identify themselves with a Client ID Metadata Document (Claude) are not stored; dynamically registered and admin-registered ones are.

## Identity

The client ID: a generated identifier, or for a CIMD client the URL of its metadata document.

## Attributes

- name: declared by the client or given by the admin
- secret hash: SHA-256 of the client secret, none for public clients
- authentication method: none, client_secret_post or client_secret_basic
- redirect URIs (at most 10)
- origin: dynamic registration or admin
- first use: when it first obtained a token

## Invariants

- Every redirect URI is on the site's allowlist, however the client registered.
- The secret is stored only as a hash and shown once.
