---
id: BR-01m4v5j7bt8723rb1rgvkrcn29
form: business-rule
title: Same capabilities as the web UI
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Same capabilities as
      the web UI
    - "openspec/specs/access-control/spec.md · Purpose"
    - "PRODUCT.md · Product Principles 1"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Goals; D1; Security review (separate groups)"
    - "local/nitro/classes/mcp/dispatcher.php · dispatcher::message"
    - "local/nitro/classes/local/access.php · access::require_course, access::require_module, access::visible_users"
  quote: >-
    Every tool MUST validate the target context (course or module) and check the
    Moodle capabilities the equivalent web UI action requires, before reading or
    changing anything.
  inferred: "Scope: the capability each kind of tool checks, read from the external functions under classes/external."
---
# Same capabilities as the web UI

## Rule

Every tool MUST validate the target context (course or module) and check the Moodle capabilities the equivalent web UI action requires, before reading or changing anything.

## Rationale

Through the AI exactly as much is possible as in the web UI, no more and no less (PRODUCT.md principle 1). Every tool is a Moodle external function that starts with `validate_context()` and `require_capability()`, so MCP and classic web service calls share one code path and the same checks (design D1). When a check fails, the message tells the AI that the web interface would not allow it either.

## Scope

Every tool, whether called through MCP or exposed by an admin as a Moodle web service. Course tools check the course context, module tools the module context, and then each checks the capability of the matching web UI action (for example `moodle/course:manageactivities` for content, `mod/assign:grade` for submissions and grades, `moodle/site:sendmessage` for messages). Separate groups mode limits users as the web UI does (security review). It does not cover the OAuth endpoints.
