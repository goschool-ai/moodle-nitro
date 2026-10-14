---
id: SM-01m4v5t40qrpw6616ptw1ejhz5
form: state-machine
title: "Self-registered client lifecycle"
entity:
  - E-01m4v5t0xbzfhgcxr05wz1wxcs
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3"
    - "local/nitro/classes/oauth/clients.php · UNUSED_LIFETIME"
  quote: "a scheduled task deleting clients without a token after 7 days; and a cap on never-used clients"
  inferred: null
---
# Self-registered client lifecycle

## Governed lifecycle

A dynamically registered client, from registration to use or clean-up.

## States

- **registered, never used**
- **in use**: it obtained a token
- **deleted**

## Transitions

- (registration) → registered: refused when the never-used cap is reached
- registered → in use: its first token
- registered → deleted: no token within 7 days (scheduled clean-up), or an admin deletes it
- in use → deleted: an admin deletes it; its connections stop working
