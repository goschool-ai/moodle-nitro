---
id: E-01m4v5t1nv9vnj77m127sbym24
form: entity
title: "Token"
used_by:
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
  - UC-01m4v5t0c3b5wt6eggggfwqy05
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/db/install.xml · local_nitro_token"
    - "local/nitro/classes/oauth/tokens.php · ACCESS_TTL, REFRESH_TTL"
  quote: "Access and refresh tokens of a grant"
  inferred: null
---
# Token

## Meaning

An access or refresh token of a connection. The access token authorises MCP calls; the refresh token gets a new pair.

## Identity

The token value, stored only as its SHA-256 hash.

## Attributes

- connection, type (access or refresh)
- used: a refresh token already rotated
- expiry: access 1 hour, refresh 30 days

## Invariants

- Presenting a rotated refresh token again revokes the whole connection.
- A token stops working when its connection is revoked, the kill switch is off, the user's auth method is disabled or the site is in maintenance.
