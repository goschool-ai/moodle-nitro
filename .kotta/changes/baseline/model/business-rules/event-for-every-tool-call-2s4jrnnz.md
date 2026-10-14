---
id: BR-01m4v5j7btrxsgv3r22s4jrnnz
form: business-rule
title: Event for every tool call
capability: audit-log
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/audit-log/spec.md · Requirement: Event for every tool call'
    - "openspec/specs/audit-log/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D8"
    - "local/nitro/README.md · 7. Administration (Log)"
    - "local/nitro/classes/mcp/dispatcher.php · dispatcher::call, dispatcher::log"
    - "local/nitro/classes/event/tool_called.php"
  quote: >-
    Every `tools/call` SHALL trigger a plugin-specific Moodle event recording
    the acting user, the OAuth client, the tool name, the course (when the tool
    targets one), the outcome (success, error, preview)
  inferred: "Scope: that the event lands in the course context only when the call carries a courseid argument, otherwise in the system context, read from dispatcher::log."
---
# Event for every tool call

## Rule

Every `tools/call` SHALL trigger a plugin-specific Moodle event recording the acting user, the OAuth client, the tool name, the course (when the tool targets one), the outcome (success, error, preview) and the time. The event SHALL appear in the standard Moodle logs report.

## Rationale

Everything the AI did in Moodle must be visible afterwards, in the log reports admins and teachers already use (audit-log Purpose). Moodle's own events from the underlying APIs follow the nitro event, so the log shows both the AI's call and its effect (design D8).

## Scope

Calls that reach a tool through the MCP dispatcher. Each one triggers one `AI tool called` event, in the course context when the call names a course and in the system context otherwise. OAuth activity and the HTTP-level refusals (401, 503) are not tool-call events.
