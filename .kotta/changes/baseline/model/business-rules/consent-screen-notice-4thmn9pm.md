---
id: BR-01m4v5j7btfvmc36fr4thmn9pm
form: business-rule
title: Consent screen notice
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/access-control/spec.md · Requirement: Consent screen notice'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks (attendees put real student data on the sandbox)"
    - "local/nitro/README.md · 4. Settings (Consent screen notice)"
    - "local/nitro/settings.php · local_nitro/consentnotice"
    - "local/nitro/oauth/authorize.php"
  quote: >-
    The admin SHALL be able to set a notice text shown on the OAuth consent
    screen, for example to state that a site is a sandbox and must not hold
  inferred: "Scope: that the notice is plain text shown as entered, not translated, read from settings.php and authorize.php."
---
# Consent screen notice

## Rule

The admin SHALL be able to set a notice text shown on the OAuth consent screen, for example to state that a site is a sandbox and must not hold real student data.

## Rationale

Attendees might put real student data on the sandbox, so the consent screen carries a notice against it (design, Risks). On a faculty site it can state the institution's rule on personal data in AI tools (admin guide, section 4).

## Scope

The consent screen of every client on the site. The notice is plain text, shown as entered and not translated, and an empty setting shows nothing. It adds to the fixed statement of what the client can reach. It does not replace it.
