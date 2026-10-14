---
id: EX-01m4v5j7bty464gb5ezp4svaw8
form: example
title: Plugin privacy registry
subjects:
  - BR-01m4v5j7btgxmz1d0tpjagnrrx
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Declared data
      handling / Scenario: Plugin privacy registry
    - "openspec/specs/access-control/spec.md · Requirement: Declared data handling"
  quote: an admin opens the site's plugin privacy compliance page
---
# Plugin privacy registry

## Given

local_nitro is installed on the site.

## When

an admin opens the site's plugin privacy compliance page

## Then

`local_nitro` is listed with the fields it stores and with the transmission of course and user data to the connected AI client
