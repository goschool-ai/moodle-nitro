---
id: BR-01m4v6110c7bjb2ac1t42wt5a1
form: business-rule
title: Admin deletes a client
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 7. Administration (OAuth clients)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Accepted for the demo: an admin can delete clients on the clients page)"
    - "local/nitro/clients.php"
    - "local/nitro/classes/oauth/clients.php · delete"
  quote: null
  inferred: "That deleting a client deletes its connections, tokens and codes, read from clients.php::delete."
---
# Admin deletes a client

## Rule

A site admin SHALL be able to delete any registered OAuth client on the OAuth clients page after confirming it. Deleting a client MUST also delete every connection, token and pending authorization code issued to it, so the client can no longer obtain or use tokens.

## Rationale

The admin guide says an admin can register a client by hand or delete one. The security review relies on this page to free the never-used client cap, because never-used dynamic clients otherwise count against it for 7 days; a bulk action is deferred to the pilot.

## Scope

The admin page `clients.php`, for site administrators, for dynamically and admin-registered clients alike. Clients known by a metadata document are not stored as rows and cannot be deleted here. A user's own revocation of one connection is a separate rule.
