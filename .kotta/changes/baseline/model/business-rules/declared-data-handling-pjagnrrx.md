---
id: BR-01m4v5j7btgxmz1d0tpjagnrrx
form: business-rule
title: Declared data handling
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Declared data
      handling
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes (privacy API declaration); Non-goals (institutional data-protection pack)"
    - "local/nitro/classes/privacy/provider.php · provider::get_metadata, provider::delete_user"
  quote: The plugin SHALL implement Moodle's privacy API.
  inferred: "Scope: which tables and external locations are declared, and that admin-registered clients survive a user's deletion with createdby cleared, read from provider.php."
---
# Declared data handling

## Rule

The plugin SHALL implement Moodle's privacy API. Its metadata SHALL declare every table it stores (OAuth clients, authorization codes, tokens, pending confirmations) with the fields and the reason they are kept, and SHALL declare the external transmission of course and user data to the AI client the user has connected. Export and deletion requests SHALL cover the plugin's own tables.

## Rationale

The proposal wants this said out loud where it matters: Moodle's privacy API declares what the plugin stores and that course data leaves for the connected client. The privacy declaration is one of the things the plugin itself must do for an institution's data protection (proposal).

## Scope

The plugin's privacy provider covers its tables of connections (grants), clients, tokens, authorization codes and pending confirmations, plus two external destinations: the connected AI client and the feedback mail. The data sits in the user's context. Deleting a user revokes their grants and tokens and removes their codes and confirmations. Clients an admin registered stay, with the reference to that admin cleared. The institutional data-protection pack is out of scope and planned for the pilot.
