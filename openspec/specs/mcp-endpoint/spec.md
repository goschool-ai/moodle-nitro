# mcp-endpoint Specification

## Purpose

Exposes Moodle as an MCP server so that an AI assistant (Claude, Copilot Cowork) can discover and call course-management tools directly, without an intermediary service.

## Requirements

### Requirement: Streamable HTTP JSON-RPC endpoint
The plugin SHALL provide a single MCP endpoint at `/local/nitro/mcp.php` that accepts JSON-RPC 2.0 messages via HTTP POST according to the MCP Streamable HTTP transport, and SHALL answer each request with a single JSON response.

#### Scenario: Initialize handshake
- **WHEN** an authenticated client POSTs an `initialize` request
- **THEN** the response contains the server name `nitro`, its version, the supported protocol version, a `tools` capability and a non-empty `instructions` string

#### Scenario: Notification without response
- **WHEN** the client POSTs a JSON-RPC notification (no `id`), such as `notifications/initialized`
- **THEN** the endpoint answers HTTP 202 with an empty body

#### Scenario: Unsupported method
- **WHEN** the client calls a JSON-RPC method the server does not implement
- **THEN** the response is a JSON-RPC error with code `-32601`

#### Scenario: GET not supported
- **WHEN** a client sends HTTP GET to the endpoint
- **THEN** the endpoint answers HTTP 405

### Requirement: Bearer token authentication
The endpoint MUST reject every request that does not carry a valid OAuth access token issued by this plugin for this endpoint, and MUST execute authenticated requests as the Moodle user the token belongs to.

#### Scenario: Missing token
- **WHEN** a request arrives without an `Authorization: Bearer` header
- **THEN** the endpoint answers HTTP 401 with a `WWW-Authenticate` header whose `resource_metadata` parameter points to the protected resource metadata document

#### Scenario: Expired or revoked token
- **WHEN** a request carries an expired, revoked or unknown token
- **THEN** the endpoint answers HTTP 401 and executes nothing

#### Scenario: Acting as the token owner
- **WHEN** a request carries a valid token of user U
- **THEN** every tool call in that request runs with U's identity and U's Moodle capabilities

### Requirement: Allowlisted tool discovery
`tools/list` SHALL return exactly the tools on the site's allowlist, each with a name, a description and a JSON Schema for its input generated from the tool's parameter definition.

#### Scenario: Listing tools
- **WHEN** an authenticated client calls `tools/list`
- **THEN** every allowlisted tool is returned with `name`, `description` and `inputSchema`, and no tool outside the allowlist is returned

#### Scenario: Admin removes a tool
- **WHEN** a site admin removes a tool from the allowlist
- **THEN** the tool disappears from `tools/list` and calling it returns an error

#### Scenario: A new version brings a new tool
- **WHEN** a plugin upgrade adds a tool
- **THEN** the upgrade adds it to the allowlist, so it is listed without the admin having to find it. For the demo the upgrade adds every tool missing from the allowlist, so a tool the admin removed earlier comes back and has to be removed again; remembering removals is left to the pilot

### Requirement: Tool invocation
`tools/call` SHALL validate the arguments against the tool's parameter definition, run the tool, and return its result as MCP tool content. Failures inside a tool SHALL be returned as a tool result with `isError: true` and a human-readable message, so the AI can react to them.

#### Scenario: Successful call
- **WHEN** a client calls an allowlisted tool with valid arguments
- **THEN** the result contains the tool's output as JSON text content and `isError` is false

#### Scenario: Invalid arguments
- **WHEN** a client calls a tool with missing or wrongly typed arguments
- **THEN** the result has `isError: true` and names the offending parameter, and nothing is changed

#### Scenario: Unknown tool
- **WHEN** a client calls a tool name that is not on the allowlist
- **THEN** the response is a JSON-RPC error with code `-32602`

#### Scenario: Permission failure
- **WHEN** a tool fails because the user lacks a required Moodle capability
- **THEN** the result has `isError: true` and names the missing capability

### Requirement: Guidance for the AI in the server
Because no client-side skill is shipped, the server SHALL carry the usage guidance itself: the `instructions` field SHALL describe the typical workflows (find the course, preview before writing, import questions before building a quiz), and each tool description SHALL state when to use the tool and what it changes.

#### Scenario: Write tool description
- **WHEN** a client lists tools
- **THEN** the description of every write tool states that it changes the course and whether it requires confirmation
