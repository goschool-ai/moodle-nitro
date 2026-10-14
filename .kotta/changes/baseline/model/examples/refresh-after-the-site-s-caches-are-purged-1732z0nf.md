---
id: EX-01m4v5j7btgjaaa3nq1732z0nf
form: example
title: Refresh after the site's caches are purged
subjects:
  - BR-01m4v5j7btkhbb5ah08j7f5h4y
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Refresh with rotation /
      Scenario: Refresh after the site's caches are purged
    - "local/nitro/tests/security_review_test.php · test_refresh_survives_a_cache_purge"
  quote: >-
    a client identified by a Client ID Metadata Document refreshes its token
    after the site's caches were purged, for example by a plugin upgrade
---
# Refresh after the site's caches are purged

## Given

A teacher connected Claude through its Client ID Metadata Document, so a connection with an unused refresh token exists, and the metadata document is no longer in the site's cache.

## When

a client identified by a Client ID Metadata Document refreshes its token after the site's caches were purged, for example by a plugin upgrade

## Then

it receives new tokens without the metadata document being fetched again, because the client is known from its existing grant; the connection does not drop
