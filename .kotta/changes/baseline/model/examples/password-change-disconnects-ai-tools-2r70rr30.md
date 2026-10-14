---
id: EX-01m4v617v8h8tmx7dp2r70rr30
form: example
title: Password change disconnects AI tools
subjects:
  - BR-01m4v610earfshm4bfz7xmnxc6
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/security_review_test.php · test_password_change_revokes_connections"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Password change disconnects AI tools

## Given

A teacher has an approved AI connection with a valid access token and refresh token.

## When

the teacher's Moodle password is changed

## Then

all of the teacher's connections and their tokens are deleted, and the next MCP request with the old access token is answered with HTTP 401
