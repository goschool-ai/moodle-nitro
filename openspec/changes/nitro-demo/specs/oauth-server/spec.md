## Purpose

Lets a teacher connect an AI assistant to Moodle by pasting one URL and logging in once through the normal Moodle login (and faculty SSO), without copying long-lived tokens. OAuth is mandatory because Copilot Cowork connectors do not support API keys.

## ADDED Requirements

### Requirement: Discovery metadata
The plugin SHALL publish OAuth 2.0 Protected Resource Metadata (RFC 9728) for the MCP endpoint and Authorization Server Metadata (RFC 8414). The protected resource metadata SHALL name the MCP endpoint URL as `resource`, exactly as a teacher enters it, list the issuer in `authorization_servers`, and list `scopes_supported` as `nitro` and `offline_access`. The authorization server metadata SHALL list the authorization, token and registration endpoints, the grant types `authorization_code` and `refresh_token`, the code challenge method `S256`, the token endpoint authentication methods `none`, `client_secret_post` and `client_secret_basic`, and `client_id_metadata_document_supported: true`. Both documents MUST be reachable through URLs served by the plugin itself: the protected resource metadata through the `resource_metadata` URL of the 401 response, and the authorization server metadata through the issuer-path-appended form `<issuer>/.well-known/openid-configuration`. When the site's web server maps the root `/.well-known/oauth-protected-resource` and `/.well-known/oauth-authorization-server` paths to the plugin, those URLs SHALL return the same documents.

#### Scenario: Client discovers through the 401
- **WHEN** a client follows the `resource_metadata` URL from a 401 response of the MCP endpoint, then fetches `<issuer>/.well-known/openid-configuration`
- **THEN** both requests succeed, the first document's `resource` equals the MCP endpoint URL, and the second lists the authorization, token and registration endpoints

#### Scenario: Site with the root rewrite
- **WHEN** the site's web server maps the root well-known paths to the plugin
- **THEN** `/.well-known/oauth-protected-resource` and `/.well-known/oauth-authorization-server` return the same documents

#### Scenario: Responses within client time limits
- **WHEN** a client requests a discovery, registration or token endpoint
- **THEN** the response arrives within 2 seconds under normal load (Claude gives up after 10 seconds)

### Requirement: Redirect URI allowlist
The admin SHALL configure the list of allowed redirect URIs. The default list SHALL contain `https://claude.ai/api/mcp/auth_callback`, `https://claude.com/api/mcp/auth_callback`, `https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect`, and the loopback redirects `http://localhost/callback` and `http://127.0.0.1/callback`. Loopback entries MUST match any port. Every client registration path (CIMD, dynamic, admin) MUST reject redirect URIs outside the list.

#### Scenario: Claude web
- **WHEN** a client uses `https://claude.ai/api/mcp/auth_callback` with the default list
- **THEN** the redirect URI is accepted

#### Scenario: Claude Code on an ephemeral port
- **WHEN** a client uses `http://localhost:53117/callback` with the default list
- **THEN** the redirect URI is accepted

#### Scenario: Unknown host
- **WHEN** a client uses a redirect URI on a host not in the list
- **THEN** the registration or authorization request is rejected

### Requirement: Client ID Metadata Documents
The authorization server SHALL accept a `client_id` that is an HTTPS URL on a host from the admin's CIMD host list (default: `claude.ai`), fetch the client metadata document from it, and treat the client as a public client with the redirect URIs and name declared there. Fetched documents SHALL be cached for at most 24 hours. The server MUST NOT fetch client metadata from hosts outside the list.

#### Scenario: Claude connects without registering
- **WHEN** Claude starts an authorization request with its client metadata document URL as `client_id`
- **THEN** the server fetches the document, checks that the request's redirect URI is declared in it and allowed, and shows the consent screen with the declared client name

#### Scenario: Client ID URL on another host
- **WHEN** an authorization request uses a URL `client_id` on a host not in the CIMD host list
- **THEN** the request is rejected without fetching anything

### Requirement: Dynamic client registration
The plugin SHALL provide an OAuth 2.0 Dynamic Client Registration endpoint (RFC 7591). It SHALL register public clients (`token_endpoint_auth_method: none`) and confidential clients, issuing a `client_secret` to the latter, because Microsoft 365 Copilot does not support registration without a secret. Clients that have not obtained a token within 7 days of registration SHALL be deleted. When the number of clients that never obtained a token exceeds an admin-set cap (default 1000), registrations SHALL be refused until the number drops. The admin SHALL be able to switch dynamic registration off.

#### Scenario: Copilot registers with a secret
- **WHEN** a client registers with `token_endpoint_auth_method: client_secret_post` and the Teams redirect URI
- **THEN** it receives a `client_id` and a `client_secret`, and the token endpoint requires that secret for this client

#### Scenario: Public registration
- **WHEN** a client registers with `token_endpoint_auth_method: none` and an allowed redirect URI
- **THEN** it receives a `client_id` and no secret, and can complete the flow with PKCE alone

#### Scenario: Abandoned client
- **WHEN** a dynamically registered client has obtained no token 7 days after registration
- **THEN** it is deleted and its `client_id` no longer works

#### Scenario: Cap reached
- **WHEN** the number of never-used clients is at the cap
- **THEN** a new registration is refused with HTTP 503 and the admin sees a warning on the settings page

#### Scenario: Dynamic registration switched off
- **WHEN** the admin switches dynamic registration off
- **THEN** the registration endpoint rejects every request, it is no longer advertised in the metadata, and existing clients keep working

### Requirement: Admin client registration
A site admin SHALL be able to register OAuth clients by hand with a name, a generated client ID, an optional client secret and one or more redirect URIs from the allowlist.

#### Scenario: Unknown client
- **WHEN** an authorization request names a client ID that is neither registered nor an allowed CIMD URL
- **THEN** the request is rejected with an error page and no redirect happens

#### Scenario: Redirect URI mismatch
- **WHEN** an authorization request uses a redirect URI that is not registered for the client
- **THEN** the request is rejected with an error page and no redirect happens

### Requirement: Authorization code flow with PKCE
The authorization endpoint SHALL implement the authorization code grant with PKCE and MUST require the `S256` code challenge method. The user SHALL authenticate through the normal Moodle login, see which client is asking for access (its name and redirect host), and explicitly approve it. When a `resource` parameter is sent, it MUST equal the MCP endpoint URL.

#### Scenario: Not logged in
- **WHEN** a user who is not logged in to Moodle opens a valid authorization request
- **THEN** they are sent to the Moodle login (including any configured SSO) and return to the consent screen afterwards

#### Scenario: User approves
- **WHEN** a logged-in user who holds `local/nitro:use` approves the consent screen
- **THEN** they are redirected to the client's redirect URI with a single-use authorization code and the original `state`

#### Scenario: Loopback client warning
- **WHEN** the client's only redirect URIs are loopback addresses
- **THEN** the consent screen shows an extra warning that the application runs on the user's own computer

#### Scenario: User without the access capability
- **WHEN** a logged-in user who does not hold `local/nitro:use` in any context opens an authorization request
- **THEN** they see an explanation that AI access is not enabled for them, and no code is issued

#### Scenario: Missing or plain PKCE
- **WHEN** an authorization request has no `code_challenge` or uses `code_challenge_method=plain`
- **THEN** the request is rejected

#### Scenario: Foreign resource
- **WHEN** an authorization request carries a `resource` other than the MCP endpoint URL
- **THEN** the request is rejected with `invalid_target`

### Requirement: Token issuance
The token endpoint SHALL accept `application/x-www-form-urlencoded` requests and exchange a valid authorization code and matching `code_verifier` for an access token bound to the user, the client and the MCP endpoint, plus a refresh token when `offline_access` was granted. Authorization codes MUST be single-use and expire within 10 minutes. Access tokens MUST expire within 1 hour. Errors MUST use RFC 6749 error codes.

#### Scenario: Valid exchange
- **WHEN** the client sends a valid code, the matching `code_verifier` and the same redirect URI
- **THEN** it receives an access token, a refresh token and `expires_in`

#### Scenario: Wrong verifier
- **WHEN** the `code_verifier` does not match the code challenge
- **THEN** the response is `invalid_grant` and the code is invalidated

#### Scenario: Code reuse
- **WHEN** an authorization code is presented a second time
- **THEN** the response is `invalid_grant`

#### Scenario: Confidential client without secret
- **WHEN** a client registered with a secret calls the token endpoint without it
- **THEN** the response is `invalid_client`

### Requirement: Refresh with rotation
The token endpoint SHALL accept a refresh token to issue a new access token and a new refresh token in the same response, and MUST invalidate the used refresh token. Refresh tokens MUST expire after at most 30 days. An invalid refresh token MUST be answered with `invalid_grant`.

#### Scenario: Refresh
- **WHEN** the client presents a valid refresh token
- **THEN** it receives a new access token and a new refresh token, and the old refresh token no longer works

#### Scenario: Refresh token reuse
- **WHEN** an already used refresh token is presented again
- **THEN** the response is `invalid_grant` and all tokens of that grant are revoked

### Requirement: Users see and revoke their connections
Every user SHALL have a page, linked from their profile, listing the AI clients they have approved, with client name, redirect host, approval time and last use, and SHALL be able to revoke each one. Revocation MUST invalidate all access and refresh tokens of that grant immediately.

#### Scenario: Teacher disconnects Claude
- **WHEN** a teacher revokes the Claude entry on their connections page
- **THEN** the next MCP request with any token of that grant is answered with HTTP 401, and the client has to go through authorization again

### Requirement: Token secrecy and validity
Tokens and client secrets MUST be stored only as hashes. A token MUST stop working when its user is suspended or deleted, when the user revokes it, or when the plugin is disabled.

#### Scenario: Suspended user
- **WHEN** a user with a valid access token is suspended in Moodle
- **THEN** the next MCP request with that token is answered with HTTP 401
