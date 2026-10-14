---
id: BR-01m4v5j7btm6qyqm6d798d1zpa
form: business-rule
title: Admin client registration
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Admin client
      registration
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (admin registration for sites that switch DCR off)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Copilot DCR connectors fail silently)"
    - "local/nitro/README.md · 7. Administration"
    - "local/nitro/clients.php"
    - "local/nitro/classes/form/client_form.php · validation"
  quote: >-
    A site admin SHALL be able to register OAuth clients by hand with a name, a
    generated client ID, an optional client secret and one or more redirect URIs
    from
  inferred: "That admin clients are exempt from the clean-up and the cap, read from clients.php::delete_unused / count_never_used and cleanup_test."
---
# Admin client registration

## Rule

A site admin SHALL be able to register OAuth clients by hand with a name, a generated client ID, an optional client secret and one or more redirect URIs from the allowlist.

## Rationale

Admin registration is the path for sites that switch dynamic registration off. It is also the fallback for Copilot, whose dynamic-registration connectors are reported to fail silently after sideloading.

## Scope

The admin page *Site administration → Plugins → Local plugins → nitro → OAuth clients* (`clients.php`), for site administrators. A confidential client gets a secret that is shown once. Redirect URIs must be on the allowlist. Admin-registered clients are never deleted by the clean-up and do not count against the never-used cap. Deleting clients is a separate rule.
