---
id: EX-01m4v6182g4qepctjb7wjzjfre
form: example
title: Teacher loses AI access
subjects:
  - BR-01m4v610earfshm4bfz7xmnxc6
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/oauth/tokens_test.php · test_refresh_after_access_withdrawn"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Teacher loses AI access

## Given

A teacher connected a client with `offline_access`, and the role that gave them `local/nitro:use` has since been removed.

## When

the client presents its refresh token

## Then

the response is `invalid_grant`, the whole connection is revoked, and its access token stops working too
