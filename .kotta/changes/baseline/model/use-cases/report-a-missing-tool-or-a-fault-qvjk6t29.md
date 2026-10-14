---
id: UC-01m4v5syt6d55ed03mqvjk6t29
form: use-case
title: "Report a missing tool or a fault"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btqgve03sy1nmcc10t
refines:
  - BR-01m4v5j7bt8fgb45bv3ex3br5x
  - BR-01m4v5j7btgddbv0vya6ypf3hc
  - BR-01m4v5x24z56j9rgcvpv0pes77
extends:
  - UC-01m4v5sw613zsg92tcczb36t32
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 4. Settings"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes"
  quote: "a tool that mails feedback about nitro to the address the admin configured, after the teacher approved the text"
  inferred: null
---
# Report a missing tool or a fault

## Intent

When the assistant hits something Nitro cannot do, or a tool behaves wrongly, the teacher reports it to the people who run Nitro after reading the exact text.

## Preconditions

The feedback tool is switched on and an address is configured.

## Main success scenario

1. The assistant offers to report the gap and drafts the text.
2. The first `send_feedback` call returns a preview; the teacher reads it and approves.
3. The mail goes to the configured address with the text, the site and the version numbers, never course or student data.

## Alternatives

- The teacher rewrites the text: a new preview.
- The mail server is unreachable: the message is queued and retried.
- The tool is switched off or no address is set: the assistant says so.
