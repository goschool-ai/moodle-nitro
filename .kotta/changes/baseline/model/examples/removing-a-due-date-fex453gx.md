---
id: EX-01m4v5zb4g7ydvg62efex453gx
form: example
title: Removing a due date
subjects:
  - BR-01m4v5z9ryn9rk18n8d8ccw7r3
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/save_assignment_test.php · test_remove_due_date"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Removing a due date

## Given

An assignment saved under a key has the due date 2026-10-09T23:59.

## When

the teacher saves it again with `due` set to `none`

## Then

the assignment has no due date, the result shows the due date as empty, and `duedate` is listed as changed
