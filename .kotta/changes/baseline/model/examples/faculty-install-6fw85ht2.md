---
id: EX-01m4v5j7btnd10vne16fw85ht2
form: example
title: Faculty install
subjects:
  - BR-01m4v5j7btsncgptdggxmhsz4e
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Independent of
      the nitro plugin / Scenario: Faculty install
    - 'local/nitro/README.md · 1. Requirements; note on local_nitrosandbox'
  quote: >-
    a faculty admin installs `local_nitro` without the sandbox onboarding
    component
  inferred: 'Given supplied from the admin guide; no test exercises it.'
---
# Faculty install

## Given

A faculty Moodle (5.0 or later) does not have the sandbox onboarding plugin installed.

## When

a faculty admin installs `local_nitro` without the sandbox onboarding component

## Then

every `local_nitro` feature works
