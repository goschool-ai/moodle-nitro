---
id: EX-01m4v5j7btprke2pntg9cerqtf
form: example
title: Unknown host
subjects:
  - BR-01m4v5j7bts0wwhsmadh9fjtda
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Redirect URI allowlist
      / Scenario: Unknown host
  quote: a client uses a redirect URI on a host not in the list
---
# Unknown host

## Given

The site's redirect URI list does not contain the host of the client's redirect URI.

## When

a client uses a redirect URI on a host not in the list

## Then

the registration or authorization request is rejected
