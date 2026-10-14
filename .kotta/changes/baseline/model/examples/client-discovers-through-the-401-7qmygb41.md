---
id: EX-01m4v5j7bt856v9s847qmygb41
form: example
title: Client discovers through the 401
subjects:
  - BR-01m4v5j7btjrh80q3a7z21xadp
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Discovery metadata /
      Scenario: Client discovers through the 401
  quote: >-
    a client follows the `resource_metadata` URL from a 401 response of the MCP
    endpoint, then fetches `<issuer>/.well-known/openid-configuration`
---
# Client discovers through the 401

## Given

nitro is enabled, and the client has POSTed to the MCP endpoint without a token and received a 401 whose `WWW-Authenticate` header carries the `resource_metadata` URL.

## When

a client follows the `resource_metadata` URL from a 401 response of the MCP endpoint, then fetches `<issuer>/.well-known/openid-configuration`

## Then

both requests succeed, the first document's `resource` equals the MCP endpoint URL, and the second lists the authorization, token and registration endpoints
