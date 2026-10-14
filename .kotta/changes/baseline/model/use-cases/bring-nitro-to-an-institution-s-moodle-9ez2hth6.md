---
id: UC-01m4v5swm98nywm4eb9ez2hth6
form: use-case
title: "Bring Nitro to an institution's Moodle"
level: summary
actor:
  - A-01m4v5svz9q5s0fwj4xnjp14j4
goal:
  - G-01m4v5saj3ph52x7vag1y62efr
includes:
  - UC-01m4v5sz8xw7h30c3nba92b51n
  - UC-01m4v5szjk7rmysaj066c8mz69
  - UC-01m4v5swwpyn1vtbq5zkjbkcv9
  - UC-01m4v5t00xh3nwy7f83gkbmrr8
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 1–7"
    - "PRODUCT.md · Users"
  quote: null
  inferred: "The journey order follows the admin installation guide."
---
# Bring Nitro to an institution's Moodle

## Intent

An institution's Moodle administrator makes Nitro available to the teachers who should have it, under the institution's rules.

## Preconditions

The administrator can install plugins and can have the web server configuration changed; the site runs Moodle 5.0 or later over HTTPS.

## Main success scenario

1. The administrator installs and switches on Nitro (Install and switch on Nitro).
2. They decide who may use it and which tools are offered (Decide who and what may use Nitro).
3. Teachers connect their own AI assistants (Connect an AI assistant to Moodle).
4. The administrator reviews what the AI did when needed (Review what the AI did).

## Alternatives

- Dynamic registration is switched off or a client needs a secret: Register an AI client by hand.
- Something goes wrong: the administrator switches Nitro off with the kill switch.
