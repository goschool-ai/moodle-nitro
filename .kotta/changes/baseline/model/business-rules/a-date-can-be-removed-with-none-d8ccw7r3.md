---
id: BR-01m4v5z9ryn9rk18n8d8ccw7r3
form: business-rule
title: A date can be removed with none
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/classes/external/save_assignment.php · execute_parameters, date"
    - "local/nitro/classes/external/create_quiz.php · execute_parameters"
    - "local/nitro/tests/external/save_assignment_test.php · test_remove_due_date"
  inferred: "The obligation is read from the parameter descriptions and save_assignment::date; the rationale is my reading of how it fits Partial updates."
---
# A date can be removed with none

## Rule

`save_assignment` SHALL remove the due date or the cut-off date when the value `none` is given for it, and SHALL list the removed date among the changed fields.

## Rationale

Republishing must be able to undo a date as well as set it, and an omitted parameter already means "keep" (Partial updates), so removing a date needs a value of its own.

## Scope

Applies to the `due` and `cutoff` parameters of `save_assignment`, on creation and on update. `create_quiz` never updates and takes an empty value for no open or close time.
