---
id: EX-01m4v5j7btpqxk7cqtbc8tkdgc
form: example
title: Not logged in
subjects:
  - BR-01m4v5j7btvq0rgyezv8jzzh1r
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Authorization code flow
      with PKCE / Scenario: Not logged in
    - "local/nitro/tests/behat/authorize.feature · A teacher who is not logged in logs in, sees who asks, and approves"
  quote: a user who is not logged in to Moodle opens a valid authorization request
---
# Not logged in

## Given

A Moodle user with `local/nitro:use` has no Moodle session, and the request comes from a registered client with an allowlisted redirect URI and an S256 code challenge.

## When

a user who is not logged in to Moodle opens a valid authorization request

## Then

they are sent to the Moodle login (including any configured SSO) and return to the consent screen afterwards
