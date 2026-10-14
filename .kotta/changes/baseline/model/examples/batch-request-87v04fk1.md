---
id: EX-01m4v61a9fv1dfs3fb87v04fk1
form: example
title: Batch request
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
# Batch request

## Given

An authenticated client.

## When

it POSTs a JSON array of JSON-RPC requests

## Then

the endpoint answers with JSON-RPC error `-32600` and runs none of them
