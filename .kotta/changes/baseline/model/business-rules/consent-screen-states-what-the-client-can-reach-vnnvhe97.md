---
id: BR-01m4v5j7btvf34sed2vnnvhe97
form: business-rule
title: Consent screen states what the client can reach
capability: access-control
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Consent screen states
      what the client can reach
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (name with redirect host); Risks (misleading client name; conversation history)"
    - "PRODUCT.md · Product Principles 3"
    - "local/nitro/templates/consent.mustache"
    - "local/nitro/oauth/authorize.php"
  quote: >-
    Before a grant is given, the consent screen SHALL state, without any admin
    configuration and in the user's language: which data the client can read
    with the user's own permissions
---
# Consent screen states what the client can reach

## Rule

Before a grant is given, the consent screen SHALL state, without any admin configuration and in the user's language: which data the client can read with the user's own permissions (course content, participant lists, submissions and grades), that whatever the AI reads goes to the provider of that client and may stay in that conversation's history outside Moodle, and that messages, announcements and grades still need a separate approval for each send. It SHALL name the client and its redirect host next to this text.

## Rationale

What the AI reads lands in the AI provider's conversation history, outside Moodle, so the consent screen says so before the teacher grants anything (design, Risks). The client's name is self-declared and proves nothing, so the redirect host is shown next to it (design D3). A cautious teacher always sees what they are allowing (PRODUCT.md principle 3).

## Scope

The OAuth consent screen, for every client, whether it uses a metadata document, registered itself or was registered by an admin. Extra warnings appear for clients on the user's own computer and for self-registered clients. The text describes the data flow. It does not control what the provider does with the data.
