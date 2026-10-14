---
id: BR-01m4v5xhg223szxb0ngrb5tc8w
form: business-rule
title: The sandbox login page explains the signup steps
capability: sandbox-onboarding
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path (the signup confirmation step blocked the attendee)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (attendees put real student data on the sandbox)'
    - 'deploy/branding.php · auth_instructions'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 10.4'
  inferred: 'The rule text is phrased from the notice set in deploy/branding.php.'
---
# The sandbox login page explains the signup steps

## Rule

The sandbox login and signup page SHALL tell a newcomer, in Hungarian and in English, how to create an account, that they MUST click the confirmation link in the email before they can log in, that their own trial course with fictitious students appears within a minute of logging in, and that real student data does not belong on the sandbox.

## Rationale

The first real signup showed that the confirmation step was easy to miss, and logging in before confirming just failed, so the login page now says the link must be clicked (Attendee path). The notice also guards against attendees putting real student data on the sandbox (Risks).

## Scope

The sandbox's login page, set by the branding script through Moodle's login instructions and the multilang filter. The separate consent screen notice belongs to access control.
