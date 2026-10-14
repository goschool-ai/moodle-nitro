---
id: EX-01m4v5j7bt5c0vy1ttah9170vw
form: example
title: Grading turned off site-wide
subjects:
  - BR-01m4v5j7bt41bfj98fr44h8jj9
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Per-tool admin
      control / Scenario: Grading turned off site-wide
    - "openspec/specs/access-control/spec.md · Requirement: Per-tool admin control"
  quote: an admin unticks `grade_submission` and a teacher's client lists tools
---
# Grading turned off site-wide

## Given

Every tool is allowed, as by default, and a teacher's client is connected.

## When

an admin unticks `grade_submission` and a teacher's client lists tools

## Then

`grade_submission` is absent from `tools/list`, calling it by name is refused, and the other tools keep working
