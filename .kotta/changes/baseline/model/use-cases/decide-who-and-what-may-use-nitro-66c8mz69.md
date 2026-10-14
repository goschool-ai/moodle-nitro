---
id: UC-01m4v5szjk7rmysaj066c8mz69
form: use-case
title: "Decide who and what may use Nitro"
level: user-goal
actor:
  - A-01m4v5svz9q5s0fwj4xnjp14j4
goal:
  - G-01m4v5j7bta2nyknr11dkc0da5
refines:
  - BR-01m4v5j7btg9g46903yer3cvj5
  - BR-01m4v5j7bt41bfj98fr44h8jj9
  - BR-01m4v5z58esa34q5kbetgkp389
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 4. Settings"
    - "local/nitro/README.md · 5. Who may use it"
  quote: "a site setting that allows or denies each tool one by one, so an admin can run a read-only site or keep grading out of the AI's reach"
  inferred: null
---
# Decide who and what may use Nitro

## Intent

The administrator decides which teachers may use Nitro and which tools are offered, and adds the institution's own notice to the consent screen.

## Preconditions

Nitro is installed.

## Main success scenario

1. The administrator grants `local/nitro:use` at site, category or course level (no role has it by default).
2. They untick the tools they do not want offered.
3. They set a consent screen notice, for example the institution's rule on personal data in AI tools.

## Alternatives

- A read-only site: every write tool unticked.
- Grading kept out of the AI's reach: `grade_submission` unticked.
