---
id: EX-01m4v5z5q3r938vfesqc85n74p
form: example
title: Fresh install grants AI access to nobody
subjects:
  - BR-01m4v5z58esa34q5kbetgkp389
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/behat/capability.feature · The capability is listed in role definitions and not allowed by default"
    - "local/nitro/tests/behat/authorize.feature · A user without AI access gets an explanation and no code"
  inferred: "Read from the two Behat scenarios together."
---
# Fresh install grants AI access to nobody

## Given

nitro has just been installed and switched on, nobody has changed any role, and a user is an editing teacher in a course.

## When

the teacher opens the consent screen of an allowed AI client

## Then

the screen says that AI access is not enabled for them, no approve button is shown, and no authorization code is issued
