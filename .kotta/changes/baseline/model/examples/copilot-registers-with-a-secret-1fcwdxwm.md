---
id: EX-01m4v5j7bt859fssm41fcwdxwm
form: example
title: Copilot registers with a secret
subjects:
  - BR-01m4v5j7bt8ewybvncggz1prdn
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Dynamic client
      registration / Scenario: Copilot registers with a secret
  quote: >-
    a client registers with `token_endpoint_auth_method: client_secret_post` and
    the Teams redirect URI
---
# Copilot registers with a secret

## Given

Dynamic registration is on, the never-used client cap is not reached, and the Teams redirect URI is on the allowlist (the default).

## When

a client registers with `token_endpoint_auth_method: client_secret_post` and the Teams redirect URI

## Then

it receives a `client_id` and a `client_secret`, and the token endpoint requires that secret for this client
