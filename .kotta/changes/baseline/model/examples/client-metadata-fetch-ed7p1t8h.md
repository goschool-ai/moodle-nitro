---
id: EX-01m4v5j7btncbhxjgeed7p1t8h
form: example
title: Client metadata fetch
subjects:
  - BR-01m4v5j7bt63kng1skzff30aq7
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: No third-party data
      flow / Scenario: Client metadata fetch
    - "local/nitro/tests/oauth/cimd_test.php · test_request_carries_no_user_data, test_host_not_in_list_is_never_fetched"
  quote: the authorization server fetches a client metadata document
  inferred: "Given: read from the CIMD tests."
---
# Client metadata fetch

## Given

A logged-in user starts authorization for a client whose client ID is an HTTPS URL, and the site has no cached copy of its metadata document.

## When

the authorization server fetches a client metadata document

## Then

the request goes only to an allowed CIMD host and contains no user or course data
