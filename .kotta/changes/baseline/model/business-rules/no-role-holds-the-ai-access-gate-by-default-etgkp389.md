---
id: BR-01m4v5z58esa34q5kbetgkp389
form: business-rule
title: No role holds the AI access gate by default
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 5. Who may use it"
    - "local/nitro/db/access.php · local/nitro:use (archetypes)"
    - "local/nitro/tests/behat/capability.feature · The capability is listed in role definitions and not allowed by default"
  inferred: "The obligation is read from db/access.php (no archetype holds the capability) and the Behat scenario. The admin guide states the fact but not as a requirement."
---
# No role holds the AI access gate by default

## Rule

The `local/nitro:use` capability SHALL NOT be allowed for any role when the plugin is installed or upgraded, and until an admin grants it through role management, users other than site administrators MUST NOT be able to connect an AI client.

## Rationale

Who may use Moodle through an AI is the admin's decision. The admin guide says no role has the gate by default and explains that it carries personal-data, spam and data-loss risk, because the AI reads participants and submissions, messages students and changes content on the teacher's behalf.

## Scope

The capability definition of local_nitro, on installation and upgrade. Site administrators hold every capability in Moodle and are not limited by it. On the sandbox, the sandbox-only plugin grants the gate to each attendee in their own demo course.
