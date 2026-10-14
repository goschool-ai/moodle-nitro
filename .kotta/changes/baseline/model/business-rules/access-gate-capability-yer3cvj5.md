---
id: BR-01m4v5j7btg9g46903yer3cvj5
form: business-rule
title: Access gate capability
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Access gate
      capability
    - "openspec/specs/access-control/spec.md · Purpose"
    - "PRODUCT.md · Product Principles 1; Capabilities and Constraints"
    - "local/nitro/README.md · 5. Who may use it"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9 (sandbox-only role nitrodemo)"
    - "local/nitro/db/access.php · local/nitro:use"
    - "local/nitro/classes/local/access.php · access::require_course"
    - "local/nitro/classes/oauth/authorization.php · authorization::user_may_connect"
  quote: >-
    The plugin SHALL define one capability, `local/nitro:use`, which is only an
    entry gate: it grants no permission beyond what the user's other Moodle
    capabilities already allow.
  inferred: "Scope: that the gate is checked per course on every tool call and, in any context, before an authorization code or token is issued, read from access::require_course and authorization::user_may_connect."
---
# Access gate capability

## Rule

The plugin SHALL define one capability, `local/nitro:use`, which is only an entry gate: it grants no permission beyond what the user's other Moodle capabilities already allow. The plugin SHALL NOT create roles or change existing role definitions. Admins SHALL be able to grant it at system, category or course level through normal role management.

## Rationale

Admins decide who may use Moodle through an AI with the role tools they already use, and the gate cannot widen anyone's power: through the AI exactly as much is possible as in the web UI, no more and no less (PRODUCT.md principle 1). The plugin creates no roles so that role design stays with the institution (PRODUCT.md). The capability carries personal-data, spam and data-loss risk, because the AI reads participants and submissions, messages students and changes content on the teacher's behalf (admin guide, section 5).

## Scope

Every MCP tool call checks the gate in the target course context, and the OAuth authorization and token endpoints refuse a user who holds it nowhere. It does not replace the per-action checks of the rule "Same capabilities as the web UI". The sandbox role `nitrodemo` is created by the sandbox-only plugin, never by local_nitro.
