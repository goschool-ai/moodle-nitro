---
id: EX-01m4v6198mjz9n12r4xqshq479
form: example
title: Discovery appended to the MCP URL
subjects:
  - BR-01m4v611jskt85yswm2rv7x4r5
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/mcp/server_test.php · test_appended_discovery_form"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Discovery appended to the MCP URL

## Given

nitro is enabled.

## When

a client without a token sends GET `/local/nitro/mcp.php/.well-known/openid-configuration`

## Then

the response is HTTP 200 with the authorization server metadata, whose `issuer` is nitro's issuer
