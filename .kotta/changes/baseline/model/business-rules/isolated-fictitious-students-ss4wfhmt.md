---
id: BR-01m4v5j7bt0e86kt0sss4wfhmt
form: business-rule
title: Isolated fictitious students
capability: sandbox-onboarding
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Isolated
      fictitious students
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9 (fresh students per course; implementation notes)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Sandbox capacity'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals (real student data on the sandbox)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 11.10'
    - 'local/nitrosandbox/classes/provisioner.php · build(), seed_assignment()'
  quote: >-
    Each personal course SHALL have its own 20 fictitious students, created for
    that course only, with submissions that include on-time, late and missing
    cases, and on-time submissions without the required
---
# Isolated fictitious students

## Rule

Each personal course SHALL have its own 20 fictitious students, created for that course only, with submissions that include on-time, late and missing cases, and on-time submissions without the required repository link. The assignment's description MUST NOT state dates of its own, because the seeded dates follow the signup day. Fictitious students MUST NOT be able to log in and MUST NOT have a deliverable email address.

## Rationale

Each course gets fresh students because shared accounts would list every attendee's course on their profile, leaking who else signed up (D9). The submission pattern gives the weekly loop (who is behind, late work, a missing link) real answers, and the assignment text carries no dates because the seeded dates move to the signup day (task 11.10). The students are `nologin` with `@example.invalid` addresses, so nobody can sign in as them and no mail leaves for them; the sandbox holds fictitious students only.

## Scope

Every demo course built by the sandbox provisioner: its students, their two groups, the seeded submissions of the demo assignment and the students' last-access data. It does not cover the attendee's own account or courses created by other means.
