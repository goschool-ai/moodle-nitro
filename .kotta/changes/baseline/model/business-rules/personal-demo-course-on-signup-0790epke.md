---
id: BR-01m4v5j7bty7qzadb40790epke
form: business-rule
title: Personal demo course on signup
capability: sandbox-onboarding
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Personal demo
      course on signup
    - 'openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why (attendees self-serve from 2026-10-07)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9 (observers, ad hoc task; Alternatives)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (many attendees sign up at once)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Sandbox capacity (latency)'
    - 'local/nitrosandbox/classes/observer.php · maybe_queue()'
    - 'local/nitrosandbox/classes/provisioner.php · provision(), build()'
  quote: >-
    When a new user completes signup on the sandbox, a personal copy of the demo
    course SHALL be created within one minute, named after a neutral sequence
    number, with the
---
# Personal demo course on signup

## Rule

When a new user completes signup on the sandbox, a personal copy of the demo course SHALL be created within one minute, named after a neutral sequence number, with the user enrolled as editing teacher and holding `local/nitro:use` in that course.

## Rationale

Attendees of the live demo must be able to use nitro on the sandbox from the next day, without anyone's help (proposal, Why). Each gets a course of their own because in one shared course attendees would see each other's changes (D9, Alternatives). The course is built in an ad hoc task so that signup stays fast when many attendees sign up at once (Risks).

## Scope

The sandbox, with `local_nitrosandbox` enabled. Moodle 5.2 has no event for email confirmation, so the task is queued at the first login of a confirmed user, or when an already confirmed user is created (D9), and the course is ready when cron runs it: about 22 seconds with the 20-second cron, about 2 minutes for the last of 30 simultaneous signups (Sandbox capacity). It does not apply to faculty sites.
