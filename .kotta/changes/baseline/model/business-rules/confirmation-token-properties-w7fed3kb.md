---
id: BR-01m4v5j7bt18sj6vqww7fed3kb
form: business-rule
title: Confirmation token properties
capability: write-confirmation
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation
      token properties
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5; Security review (single use under a lock; not bound to the OAuth grant)"
    - "local/nitro/classes/local/confirmation.php · confirmation::issue, confirmation::redeem, confirmation::args_hash"
  quote: >-
    A confirmation token MUST be single-use, bound to the user, the tool and a
    hash of the arguments, and MUST expire within 10 minutes.
---
# Confirmation token properties

## Rule

A confirmation token MUST be single-use, bound to the user, the tool and a hash of the arguments, and MUST expire within 10 minutes.

## Rationale

Binding the token to the user, the tool and a hash of the canonicalised arguments makes a correctly repeated call match, and any change needs a new preview (design D5). Tokens are claimed under a lock, so two parallel calls cannot both execute one approval (security review).

## Scope

Every confirmation token. Only its hash is stored. The argument hash ignores `dry_run`, the token itself and key order. Accepted for the demo: the token is not bound to the OAuth grant (security review).
