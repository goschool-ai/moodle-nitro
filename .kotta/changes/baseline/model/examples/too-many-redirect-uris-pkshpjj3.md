---
id: EX-01m4v618ee2gr7802bpkshpjj3
form: example
title: Too many redirect URIs
subjects:
  - BR-01m4v610tbvbp1m7df2ywz0gp4
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/security_review_test.php · test_registration_limits"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Too many redirect URIs

## Given

Dynamic client registration is on and the never-used client cap is not reached.

## When

a client registers with 11 redirect URIs

## Then

the response is HTTP 400 `invalid_redirect_uri` and no client is registered
