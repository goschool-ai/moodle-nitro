---
id: E-01m4v5t2wx7bx7196k5hsx8wrt
form: entity
title: "Fictitious student"
used_by:
  - UC-01m4v5sz31mn30kpk5nqksd170
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Sandbox capacity"
  quote: "creates 20 fictitious students for this course only (auth=nologin, email on an unroutable domain such as @example.invalid)"
  inferred: null
---
# Fictitious student

## Meaning

A made-up student of one trial course, with submissions to work with.

## Identity

A user account whose username is based on the course ID.

## Attributes

- name, groups
- submission: on time, late, missing, on time without the repository link, or with a decision explained

## Invariants

- Enrolled in exactly one trial course.
- Cannot log in and has no deliverable email address.
