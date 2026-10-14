---
id: EX-01m4v5zbag6dqptehz2znq2amq
form: example
title: Date without a time
subjects:
  - BR-01m4v5z9ywbqaw6d4n303nmket
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/local/content_test.php · test_bad_date"
    - "local/nitro/classes/local/dates.php · parse"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Date without a time

## Given

A teacher saves an assignment through `save_assignment`.

## When

the due date is given as `2026-10-09`, with no time

## Then

nothing is saved and the error says that `due` is not an ISO 8601 date and time, with an example such as `2026-10-09T23:59`
