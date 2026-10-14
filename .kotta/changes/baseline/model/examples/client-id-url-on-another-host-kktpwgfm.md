---
id: EX-01m4v5j7bttpmgsqq2kktpwgfm
form: example
title: Client ID URL on another host
subjects:
  - BR-01m4v5j7bt9ahkexfas7g324tf
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Client ID Metadata
      Documents / Scenario: Client ID URL on another host
    - "local/nitro/tests/oauth/cimd_test.php · test_host_not_in_list_is_never_fetched"
  quote: >-
    an authorization request uses a URL `client_id` on a host not in the CIMD
    host list
---
# Client ID URL on another host

## Given

The CIMD host list contains only `claude.ai` (the default).

## When

an authorization request uses a URL `client_id` on a host not in the CIMD host list

## Then

the request is rejected without fetching anything
