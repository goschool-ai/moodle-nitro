---
id: EX-01m4v5j7btw64nrjkt8y0q4fnf
form: example
title: Gate granted in one course only
subjects:
  - BR-01m4v5j7btg9g46903yer3cvj5
  - UC-01m4v5szjk7rmysaj066c8mz69
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: Access gate
      capability / Scenario: Gate granted in one course only
    - "local/nitro/tests/mcp/dispatcher_test.php · test_gate_granted_in_one_course_only"
  quote: >-
    a teacher holds `local/nitro:use` only in course A and calls a tool on
    course B where they are also a teacher
  inferred: "Given: read from the dispatcher test."
---
# Gate granted in one course only

## Given

nitro is switched on, the teacher is an editing teacher in courses A and B, and a role granting `local/nitro:use` is assigned to them only in course A.

## When

a teacher holds `local/nitro:use` only in course A and calls a tool on course B where they are also a teacher

## Then

the call fails with a permission error and nothing is changed in course B
