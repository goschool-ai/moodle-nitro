---
id: EX-01m4v5j7btkrk8eyt1qm6gjg5p
form: example
title: Attendee opens the sandbox
subjects:
  - BR-01m4v5j7bt3m6w1zws5jbkcvjw
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: GoSchool-branded
      sandbox / Scenario: Attendee opens the sandbox
    - 'deploy/branding.php'
  quote: an attendee opens the sandbox login page or their demo course
  inferred: 'Given supplied from the sandbox deployment script that applies the branding.'
---
# Attendee opens the sandbox

## Given

GoSchool branding has been applied to the sandbox: site name, logos, favicon, brand colour and the login page notice.

## When

an attendee opens the sandbox login page or their demo course

## Then

they see the GoSchool logo, colours and site name, not the default Moodle look
