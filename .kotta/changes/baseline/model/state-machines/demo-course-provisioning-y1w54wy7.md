---
id: SM-01m4v5t4788c97v9vfy1w54wy7
form: state-machine
title: "Demo course provisioning"
entity:
  - E-01m4v5t2p43bn1mkz302hhpgak
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Sandbox capacity"
    - "local/nitrosandbox/classes/observer.php"
  quote: "observers on user_loggedin and user_created queue an ad hoc task for a confirmed user without a demo course yet"
  inferred: null
---
# Demo course provisioning

## Governed lifecycle

A trial course, from the teacher's sign-up to a ready course.

## States

- **none**: the account is not confirmed, or onboarding is off
- **queued**: marked for the teacher, waiting for cron
- **ready**
- **failed**: the half-built course was deleted

## Transitions

- none → queued: the first login of a confirmed user (or creation of an already confirmed one)
- queued → ready: the ad hoc task restores the template, creates the students and submissions, enrols the teacher
- queued → failed → queued: provisioning fails; the half-built course is deleted so a retry starts clean
