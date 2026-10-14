---
id: BR-01m4v5j7btgx9v6ccgw8azb6hg
form: business-rule
title: Token secrecy and validity
capability: oauth-server
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Token secrecy and
      validity
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (tokens and secrets stored as SHA-256 hashes)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (open registration public)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Accepted for the demo)"
    - "local/nitro/classes/oauth/tokens.php · validate_access"
  quote: Tokens and client secrets MUST be stored only as hashes.
---
# Token secrecy and validity

## Rule

Tokens and client secrets MUST be stored only as hashes. A token MUST stop working when its user is suspended or deleted, when the user revokes it, or when the plugin is disabled.

## Rationale

Tokens act with the teacher's own Moodle permissions on an OAuth server that is public on the internet with open registration, so the design keeps it minimal: random 256-bit tokens and secrets stored only as SHA-256 hashes, so a copy of the database yields nothing usable, and short lifetimes. A token must also die with the account, with the user's own revocation and with the site kill switch, so that disabling a user or the plugin takes the AI's access away at once.

## Scope

Access and refresh tokens, authorization codes and confidential client secrets in the plugin tables (`local_nitro_token`, `local_nitro_code`, `local_nitro_client`); validity is checked on every MCP request. It does not cover what the design accepted for the demo: switching the kill switch off and on again revives unexpired tokens, and forced password change or an unaccepted site policy are not checked. The kill switch setting itself belongs to access control.
