---
id: EX-01m4v5j7btnjtvfhft3qrrarbw
form: example
title: Token reuse
subjects:
  - BR-01m4v5j7bt18sj6vqww7fed3kb
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation
      token properties / Scenario: Token reuse
    - "openspec/specs/write-confirmation/spec.md · Requirement: Confirmation token properties"
    - "local/nitro/tests/local/confirmation_test.php · test_token_reuse"
  quote: a confirmation token that has already been used is presented again
---
# Token reuse

## Given

A confirmation token has already been used to execute the approved action.

## When

a confirmation token that has already been used is presented again

## Then

nothing is executed and the result asks for a new preview
