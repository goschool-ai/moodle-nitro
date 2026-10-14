---
id: EX-01m4v5j7bt61m8xvj8n412qy9v
form: example
title: Claude connects without registering
subjects:
  - BR-01m4v5j7bt9ahkexfas7g324tf
  - UC-01m4v5swcbnv1j08a3n056jh08
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Client ID Metadata
      Documents / Scenario: Claude connects without registering
    - "local/nitro/tests/oauth/cimd_test.php · test_claude_connects_without_registering"
  quote: >-
    Claude starts an authorization request with its client metadata document URL
    as `client_id`
  inferred: "The document contents and the logged-in teacher, read from cimd_test."
---
# Claude connects without registering

## Given

nitro is enabled, `claude.ai` is on the CIMD host list (the default), Claude's metadata document at its client ID URL names itself and declares `https://claude.ai/api/mcp/auth_callback`, which is on the redirect allowlist, and the teacher is logged in to Moodle with `local/nitro:use`.

## When

Claude starts an authorization request with its client metadata document URL as `client_id`

## Then

the server fetches the document, checks that the request's redirect URI is declared in it and allowed, and shows the consent screen with the declared client name
