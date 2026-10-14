---
id: UC-01m4v5sz8xw7h30c3nba92b51n
form: use-case
title: "Install and switch on Nitro"
level: user-goal
actor:
  - A-01m4v5svz9q5s0fwj4xnjp14j4
goal:
  - G-01m4v5j7bta2nyknr11dkc0da5
refines:
  - BR-01m4v5j7btp3h0hp8a4zm164c0
  - BR-01m4v5j7bt0mhsaby7fyp9a02d
  - BR-01m4v5j7btgxmz1d0tpjagnrrx
  - BR-01m4v5j7btsncgptdggxmhsz4e
  - BR-01m4v5z5gtqc7gaajtjvg31w5p
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 2–4"
  quote: "nitro is installed switched off. Nothing answers until you switch it on in section 4."
  inferred: null
---
# Install and switch on Nitro

## Intent

The administrator installs Nitro, makes the discovery URLs work and switches it on, and can switch it off at once.

## Preconditions

Plugin installation rights on a Moodle 5.0+ site served over HTTPS.

## Main success scenario

1. The administrator copies the plugin to `local/nitro` and runs the upgrade; Nitro is installed switched off.
2. They add the web server rule for the root discovery URLs and make sure the Authorization header reaches PHP.
3. The Discovery check on the settings page shows every discovery URL green.
4. They switch on Enable nitro.

## Alternatives

- The discovery check shows a missing rule: it prints the rule for the web server team.
- Something goes wrong later: switching Enable nitro off refuses every request and stops existing tokens at once.
- The site is the trial Moodle: the sandbox plugin is installed too; an institutional site never needs it.
