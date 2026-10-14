---
id: EX-01m4v5j7bt1ckwfqmn4e6g5c3g
form: example
title: Site without rewrite
subjects:
  - BR-01m4v5j7btp3h0hp8a4zm164c0
  - UC-01m4v5sz8xw7h30c3nba92b51n
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Discovery self-check
      / Scenario: Site without rewrite
    - "local/nitro/tests/local/selfcheck_test.php · test_site_without_rewrite"
    - "local/nitro/classes/admin/setting_selfcheck.php · output_html"
  quote: an admin opens the settings page on a site without the rewrite rule
  inferred: "Given: that nitro must be switched on for the check to run, read from setting_selfcheck.php."
---
# Site without rewrite

## Given

nitro is switched on, the plugin-served discovery URLs answer, and the web server has no root `/.well-known/` rewrite.

## When

an admin opens the settings page on a site without the rewrite rule

## Then

the plugin-served discovery URLs are shown as working, the root URLs as missing, and the rewrite rule for the detected web server is shown as optional
