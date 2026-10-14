---
id: EX-01m4v5zaqq6rg91tpsr7x9s9sb
form: example
title: Update lists what changed
subjects:
  - BR-01m4v5z9c3gqb7gp2fjaaxbjjp
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/save_assignment_test.php · test_description_only_update"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Update lists what changed

## Given

An assignment with the key `hw2` exists, created through `save_assignment` with a name, both submission types, 20 points and due and cut-off dates.

## When

the teacher saves it again with a new description only

## Then

the result says `updated` and lists only `intro` as changed
