---
id: EX-01m4v61a38h868hxcshe59g9km
form: example
title: Body that is not JSON
subjects:
  - BR-01m4v6127a9gy8r5gvv9dbcb6p
capability: mcp-endpoint
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/mcp/server_test.php · test_parse_error_and_batch"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Body that is not JSON

## Given

An authenticated client.

## When

it POSTs the body `{not json`

## Then

the endpoint answers HTTP 400 with JSON-RPC error `-32700` and runs nothing
