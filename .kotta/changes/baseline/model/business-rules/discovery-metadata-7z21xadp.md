---
id: BR-01m4v5j7btjrh80q3a7z21xadp
form: business-rule
title: Discovery metadata
capability: oauth-server
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/oauth-server/spec.md · Requirement: Discovery metadata'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D4 Discovery: plugin-served, root rewrite where possible"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3a What Claude actually does"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Context (admins often cannot change the web server)"
    - "local/nitro/README.md · 3. Web server"
    - "local/nitro/classes/oauth/metadata.php · authorization_server, for_path"
  quote: >-
    The plugin SHALL publish OAuth 2.0 Protected Resource Metadata (RFC 9728)
    for the MCP endpoint and Authorization Server Metadata (RFC 8414).
---
# Discovery metadata

## Rule

The plugin SHALL publish OAuth 2.0 Protected Resource Metadata (RFC 9728) for the MCP endpoint and Authorization Server Metadata (RFC 8414). The protected resource metadata SHALL name the MCP endpoint URL as `resource`, exactly as a teacher enters it, list the issuer in `authorization_servers`, and list `scopes_supported` as `nitro` and `offline_access`. The authorization server metadata SHALL list the authorization, token and registration endpoints, the grant types `authorization_code` and `refresh_token`, the code challenge method `S256`, the token endpoint authentication methods `none`, `client_secret_post` and `client_secret_basic`, and `client_id_metadata_document_supported: true`. Both documents MUST be reachable through URLs served by the plugin itself: the protected resource metadata through the `resource_metadata` URL of the 401 response, and the authorization server metadata through the issuer-path-appended form `<issuer>/.well-known/openid-configuration`. When the site's web server maps the root `/.well-known/oauth-protected-resource` and `/.well-known/oauth-authorization-server` paths to the plugin, those URLs SHALL return the same documents.

## Rationale

MCP clients find the authorization server in two steps, and many faculty admins cannot change the web server configuration. So the plugin serves both documents from its own paths: the issuer is the metadata script, which places the issuer-appended form inside Moodle's tree. The 2026-09-20 probe showed that Claude asks for the root insert form, so the root rewrite is also documented and in practice required for Claude. The document carries the OpenID fields a strict client may check.

## Scope

`/local/nitro/oauth/metadata.php` and its slash-argument paths, the optional root `/.well-known/` rewrite, and the form appended to the MCP URL (its own rule). The registration endpoint is listed only while dynamic registration is on. Not covered: ID tokens and OpenID Connect. The admin self-check of these URLs belongs to access control.
