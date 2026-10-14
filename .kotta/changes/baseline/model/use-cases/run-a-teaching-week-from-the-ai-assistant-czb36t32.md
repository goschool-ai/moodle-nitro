---
id: UC-01m4v5sw613zsg92tcczb36t32
form: use-case
title: "Run a teaching week from the AI assistant"
level: summary
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5saj3ph52x7vag1y62efr
includes:
  - UC-01m4v5sxekdm6842h7fkdsmx6a
  - UC-01m4v5sxq7mrw8dcspk5n11dd7
  - UC-01m4v5sy75gyt5ne3m1n3xk85j
  - UC-01m4v5sydbccy34s28xmqtfd5k
  - UC-01m4v5sym7ws6zscx731a5ksp5
  - UC-01m4v5sxzyxjm0ekr72gwwwwq1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "PRODUCT.md · Operating Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "docs/index.html · hero"
  quote: "Its weekly loop: who is behind; individual reminders; accepting submissions; announcements; pages and assignments republished from markdown."
  inferred: "The order of the steps as one summary journey; the sources list the loop, not an order."
---
# Run a teaching week from the AI assistant

## Intent

The teacher gets the routine Moodle work of a teaching week done from one conversation in their AI assistant, and nothing reaches students without their approval.

## Preconditions

The teacher's AI assistant is connected to their Moodle (Connect an AI assistant to Moodle) and they hold `local/nitro:use` in the course.

## Main success scenario

1. The teacher asks where the course stands (Check where the course stands).
2. They follow up with the students who are behind (Follow up with students who are behind).
3. They publish or update pages and assignments (Publish pages and assignments).
4. They build a practice quiz (Build a quiz).
5. They grade submissions with feedback (Grade submissions with feedback).
6. They post an announcement (Post an announcement).

## Alternatives

- The steps happen in any order and any number of times during the week; each one is its own request.
- The assistant hits something Nitro has no tool for: Report a missing tool or a fault.
