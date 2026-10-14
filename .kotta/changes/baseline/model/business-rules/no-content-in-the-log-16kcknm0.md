---
id: BR-01m4v5j7bt5v2zqqzj16kcknm0
form: business-rule
title: No content in the log
capability: audit-log
provenance:
  level: partly-inferred
  decided_by: agent-proposed-human-approved
  sources:
    - ".kotta/changes/baseline/conversation.md · J1"
    - 'openspec/specs/audit-log/spec.md · Requirement: No content in the log'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D8"
    - "local/nitro/classes/event/tool_called.php · validate_data"
    - "local/nitro/README.md · 7. Administration (Log)"
  quote: >-
    The tool-call event MUST NOT store message bodies, page content or question
    text; it SHALL store only identifiers of the affected objects.
  inferred: "Rationale chosen by the product owner from the options the agent proposed (J1, answer 2)."
---
# No content in the log

## Rule

The tool-call event MUST NOT store message bodies, page content or question text; it SHALL store only identifiers of the affected objects.

## Rationale

The design sets what the event may store: only the tool name, the client ID, the outcome and the IDs of affected objects (design D8). Content is left out for two reasons. The log is kept long and read by more people than the original message, and content in it would survive a deletion request. And the content already lives in its own place in Moodle (messages, forum, page), so the log only has to point to it.

## Scope

The nitro tool-call event and its `other` field. The event class refuses any key other than tool, client ID, outcome and object IDs. Moodle's own events from the underlying APIs are outside this rule.
