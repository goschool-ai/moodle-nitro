---
id: BR-01m4v5j7bt7gnewzbmexq090x9
form: business-rule
title: Users see and revoke their connections
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Users see and revoke
      their connections
    - "PRODUCT.md · Purpose (can take it back at any time)"
    - "PRODUCT.md · Users (the \"My connections\" page)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (profile page lists grants and revokes them)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (no RFC 7009 endpoint)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals (admin view of all issued tokens)"
    - "local/nitro/connections.php"
    - "local/nitro/lib.php · local_nitro_myprofile_navigation"
  quote: >-
    Every user SHALL have a page, linked from their profile, listing the AI
    clients they have approved, with client name, redirect host, approval time
    and last use, and SHALL be
  inferred: "The confirmation step and the profile section the link appears in, read from connections.php and lib.php."
---
# Users see and revoke their connections

## Rule

Every user SHALL have a page, linked from their profile, listing the AI clients they have approved, with client name, redirect host, approval time and last use, and SHALL be able to revoke each one. Revocation MUST invalidate all access and refresh tokens of that grant immediately.

## Rationale

The product counts as a success when a teacher connects with one URL, sees what they are allowing and can take it back at any time. The connections page is one of the three places where teachers meet nitro. Users revoke here instead of through an RFC 7009 revocation endpoint.

## Scope

`/local/nitro/connections.php`, linked as "Connected AI tools" from the user's own profile. It lists only that user's grants, and guests have no page. Revoking asks for confirmation and deletes the grant together with every token issued under it. An admin view of all issued tokens is deferred to the pilot.
