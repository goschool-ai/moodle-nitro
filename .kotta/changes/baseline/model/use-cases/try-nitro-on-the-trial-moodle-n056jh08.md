---
id: UC-01m4v5swcbnv1j08a3n056jh08
form: use-case
title: "Try Nitro on the trial Moodle"
level: summary
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7bt9ggtwqtfebk4wy3q
  - G-01m4v5saj3ph52x7vag1y62efr
includes:
  - UC-01m4v5sz31mn30kpk5nqksd170
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
  - UC-01m4v5sw613zsg92tcczb36t32
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "docs/index.html · Try it in three steps"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Attendee path"
  quote: "Attendees must be able to use nitro themselves from the next day (2026-10-07), on the sandbox, without anyone's help."
  inferred: "The three steps as the landing page tells them (2026-10-13)."
---
# Try Nitro on the trial Moodle

## Intent

A teacher who has heard of Nitro tries it on their own, without anyone's help, on a trial course with fictitious students.

## Preconditions

The teacher has an AI assistant that accepts a custom connector (in Claude, any plan) and an email address.

## Main success scenario

1. The teacher signs up on the trial Moodle and gets their own course (Get a trial course).
2. They connect their AI assistant to the trial Moodle with the trial account (Connect an AI assistant to Moodle).
3. They run part of a teaching week on the trial course (Run a teaching week from the AI assistant).

## Alternatives

- The teacher does not click the confirmation link in the email: they cannot log in and no course is created until they do.
- The teacher wants Nitro on their own university's Moodle: Bring Nitro to an institution's Moodle.
