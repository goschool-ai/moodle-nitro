---
id: BR-01m4v5j7bt41bfj98fr44h8jj9
form: business-rule
title: Per-tool admin control
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Per-tool admin
      control
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes (read-only site, grading out of reach)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks (per-tool control); Attendee path (allowlist on upgrade)"
    - "local/nitro/README.md · 4. Settings (Allowed tools)"
    - "local/nitro/settings.php · local_nitro/tools"
    - "local/nitro/classes/local/tools.php · tools::allowed"
    - "local/nitro/classes/mcp/registry.php · registry::function_info, registry::list"
  quote: >-
    A site setting SHALL list every tool the plugin can expose with a checkbox
    for each, so that an admin can allow or deny tools one by one.
  inferred: "Scope: that the allowlist is read on every request and that send_feedback also needs its own feedback switch, read from tools::allowed."
---
# Per-tool admin control

## Rule

A site setting SHALL list every tool the plugin can expose with a checkbox for each, so that an admin can allow or deny tools one by one. This setting is the allowlist that tool discovery and invocation enforce. Every tool SHALL be enabled by default, because each one is already bounded by the user's Moodle capabilities; the setting narrows that surface further and MUST take effect without reconnecting a client.

## Rationale

An admin can run a read-only site or keep grading out of the AI's reach (proposal). Per-tool control is also one of the data-protection measures, because it narrows what is offered at all (design, Risks). The rule itself says why every tool is on by default.

## Scope

The site-wide `Allowed tools` setting, which `tools/list` and `tools/call` read on every request. It applies to every user and client alike, and per course or per user access is the gate's job. `send_feedback` also needs its own feedback switch. An upgrade adds new tools to the allowlist.
