---
id: BR-01m4v5j7bt0mhsaby7fyp9a02d
form: business-rule
title: Site kill switch
capability: access-control
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/access-control/spec.md · Requirement: Site kill switch'
    - "openspec/specs/access-control/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Migration Plan (rollback); Security review (kill switch inside the tools; revived tokens)"
    - "local/nitro/README.md · 4. Settings (Enable nitro)"
    - "local/nitro/classes/mcp/server.php · server::handle"
    - "local/nitro/classes/oauth/tokens.php · tokens::validate_access"
    - "local/nitro/classes/local/access.php · access::require_course"
  quote: A site-level setting SHALL enable or disable the plugin.
---
# Site kill switch

## Rule

A site-level setting SHALL enable or disable the plugin. While disabled, the MCP endpoint and the OAuth endpoints MUST refuse all requests and existing tokens MUST NOT work.

## Rationale

Admins must be able to switch AI access off with the tools they already use (access-control Purpose). The switch is also the rollback path if something goes wrong (design, Migration Plan).

## Scope

The site setting `Enable nitro`. While it is off, the MCP endpoint answers 503, the authorize, token, register and metadata endpoints refuse, access tokens fail validation, and tools called directly as a Moodle web service fail too (security review). Accepted limitation: switching it back on revives tokens that have not expired, so ending connections needs revocation on the clients page.
