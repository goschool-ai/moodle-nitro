---
id: SM-01m4v5t3r1f6twjmpq2cbadn4j
form: state-machine
title: "Authorization code lifecycle"
entity:
  - E-01m4v5t17fny2vjtr8rr2hr059
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3"
    - "local/nitro/classes/oauth/authorization.php"
  quote: "Codes are deleted on redemption rather than flagged as used"
  inferred: null
---
# Authorization code lifecycle

## Governed lifecycle

An authorization code, from consent to redemption.

## States

- **issued**
- **redeemed** (deleted)
- **expired**

## Transitions

- (consent) → issued
- issued → redeemed: the client exchanges it with the matching verifier and redirect URI
- issued → expired: after 5 minutes
- redeemed: presenting it again is refused
