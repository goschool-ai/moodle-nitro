---
id: BR-01m4v611cemfb81e81333p2bdv
form: business-rule
title: Self-registered clients are marked unverified on the consent screen
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (Fixed: dynamically registered clients are labelled as self-registered and unverified)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (a self-declared name proves nothing)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (malicious client with misleading name)"
    - "local/nitro/oauth/authorize.php · selfregistered"
    - "local/nitro/templates/consent.mustache"
    - "local/nitro/lang/en/local_nitro.php · consentselfregistered"
  quote: "dynamically registered clients are labelled as self-registered and unverified"
  inferred: "The warning wording and that only self-registered clients get it, read from authorize.php, consent.mustache and the language file."
---
# Self-registered clients are marked unverified on the consent screen

## Rule

When the requesting client registered itself through dynamic client registration, the consent screen SHALL warn the user that the application registered itself, that its name is not verified, and that they should allow it only if they started the connection themselves from the application they expect.

## Rationale

A self-declared name proves nothing, and a malicious client may register under a misleading name. The 2026-09-20 security review therefore had self-registered clients labelled as self-registered and unverified on the consent screen.

## Scope

The consent screen of `/local/nitro/oauth/authorize.php`, for self-registered clients only. Admin-registered clients and clients known by a metadata document from an allowlisted host get no such label. What the consent text says about data is an access-control rule, and the loopback warning belongs to the authorization flow rule.
