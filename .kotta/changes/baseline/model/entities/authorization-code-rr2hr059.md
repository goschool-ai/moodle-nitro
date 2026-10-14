---
id: E-01m4v5t17fny2vjtr8rr2hr059
form: entity
title: "Authorization code"
used_by:
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/db/install.xml · local_nitro_code"
    - "local/nitro/classes/oauth/authorization.php · CODE_TTL"
  quote: "Single-use authorization codes"
  inferred: null
---
# Authorization code

## Meaning

The single-use authorization code Moodle hands the client after the teacher approves, bound to that client, redirect URI, user and PKCE challenge.

## Identity

The code value, stored only as its SHA-256 hash.

## Attributes

- client ID, user, redirect URI, PKCE S256 challenge, scope
- client name shown at consent
- expiry (5 minutes)

## Invariants

- It can be redeemed once; redemption deletes it.
- Redemption requires the matching verifier, client and redirect URI.
