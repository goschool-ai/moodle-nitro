---
id: UC-01m4v5sz31mn30kpk5nqksd170
form: use-case
title: "Get a trial course"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7bt9ggtwqtfebk4wy3q
refines:
  - BR-01m4v5j7bty7qzadb40790epke
  - BR-01m4v5j7bt0e86kt0sss4wfhmt
  - BR-01m4v5j7bt3m6w1zws5jbkcvjw
  - BR-01m4v5xh9knmgmbt9cmjyxb435
  - BR-01m4v5xh3edmajdb0n0mwp9dxj
  - BR-01m4v5xhg223szxb0ngrb5tc8w
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9. Sandbox onboarding as a separate plugin"
    - "openspec/specs/sandbox-onboarding/spec.md · Purpose"
  quote: "signing up gives them a personal, realistic course with fictitious students, isolated from every other attendee"
  inferred: null
---
# Get a trial course

## Intent

A teacher signs up on the trial Moodle and gets their own trial course, with fictitious students and submissions to work with, isolated from every other teacher.

## Preconditions

The trial Moodle allows email self-registration.

## Main success scenario

1. The teacher creates an account on the trial Moodle.
2. Moodle emails a confirmation link; the teacher clicks it.
3. At the first login after confirming, a personal course is queued and created within a minute: the demo material, an assignment, 20 fictitious students with on-time, late and missing submissions.
4. The teacher is its editing teacher and may use Nitro in it.

## Alternatives

- The teacher logs in before confirming: login fails and the sign-up notice says why.
- Many teachers sign up at once: courses are created one after another from the queue.
