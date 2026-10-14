---
id: E-01m4v5t32wgvnbzb0kqnrnbe6m
form: entity
title: "Feedback message"
used_by:
  - UC-01m4v5syt6d55ed03mqvjk6t29
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/README.md · 4. Settings"
  quote: "The mail carries only the teacher's text, the site name and URL and version numbers, never course or student data"
  inferred: null
---
# Feedback message

## Meaning

A teacher's report about Nitro, mailed to the feedback address after the teacher approved the text.

## Identity

One mail per approved report.

## Attributes

- the teacher's text, the site name and URL, the Nitro and Moodle version numbers
- recipient: the configured feedback address

## Invariants

- It carries no course or student data.
- It is sent only after the teacher approved the exact text.
