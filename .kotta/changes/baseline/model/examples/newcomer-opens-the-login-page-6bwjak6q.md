---
id: EX-01m4v5xkcrbr01exp56bwjak6q
form: example
title: Newcomer opens the login page
capability: sandbox-onboarding
subjects:
  - BR-01m4v5xhg223szxb0ngrb5tc8w
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'deploy/branding.php · auth_instructions'
  inferred: 'Read from the deployment script; no test covers it.'
---
# Newcomer opens the login page

## Given

The sandbox's branding and notices have been applied, and a visitor has no account yet.

## When

they open the sandbox login page in English

## Then

they read that they should click Create new account, must click the link in the confirmation email (and check spam), get their own trial course within a minute of logging in, and must not add real student data
