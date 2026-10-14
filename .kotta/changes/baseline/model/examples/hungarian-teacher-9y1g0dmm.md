---
id: EX-01m4v5j7btpmtg71wh9y1g0dmm
form: example
title: Hungarian teacher
subjects:
  - BR-01m4v5j7btmeyx8k36s18qsr0y
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Hungarian and English
      / Scenario: Hungarian teacher
    - "openspec/specs/access-control/spec.md · Requirement: Hungarian and English"
    - "local/nitro/lang/hu/local_nitro.php"
  quote: a teacher whose Moodle language is Hungarian opens the consent screen
  inferred: "Given: that the Hungarian language pack must be installed, a Moodle precondition."
---
# Hungarian teacher

## Given

The Hungarian language pack is installed, the teacher's Moodle language is Hungarian, and the teacher holds `local/nitro:use`.

## When

a teacher whose Moodle language is Hungarian opens the consent screen

## Then

every text on it, including the admin-independent explanations, is in Hungarian
