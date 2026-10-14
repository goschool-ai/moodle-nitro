---
id: BR-01m4v5j7btmeyx8k36s18qsr0y
form: business-rule
title: Hungarian and English
capability: access-control
overall: true
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/access-control/spec.md · Requirement: Hungarian and English'
    - "PRODUCT.md · Users; Capabilities and Constraints; Product Principles 5; Accessibility & Inclusion"
    - "local/nitro/lang/en/local_nitro.php"
    - "local/nitro/lang/hu/local_nitro.php"
    - "local/nitro/classes/mcp/server.php · server::INSTRUCTIONS"
  quote: >-
    Every user-facing string of the plugin (consent screen, connections page,
    settings, errors, tool error messages) SHALL be available in Hungarian and
    English, and SHALL follow the user's Moodle language.
  inferred: "Scope: that the server instructions and tool descriptions, written for the AI, are English only, read from server.php and the external function descriptions."
---
# Hungarian and English

## Rule

Every user-facing string of the plugin (consent screen, connections page, settings, errors, tool error messages) SHALL be available in Hungarian and English, and SHALL follow the user's Moodle language.

## Rationale

The users are teachers at Hungarian universities, many of them cautious and new to AI connectors. The product promises the same functionality and the same product truth in Hungarian and in English, and calls this required, not optional (PRODUCT.md).

## Scope

Every string of local_nitro shown to people: the consent screen, the connections page, settings, privacy declarations and errors. The server instructions and tool descriptions address the AI and are written in English. An admin's own notice text is shown as entered.
