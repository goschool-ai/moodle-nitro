---
id: EX-01m4v5xj5z3nfnzpwz5yjttja7
form: example
title: Student of another group
capability: messaging
subjects:
  - BR-01m4v5xgej11sz0kb9pgv0qvwc
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/tests/security_review_test.php · test_separate_groups_limit_what_a_teacher_sees'
  inferred: 'Read from the test, which exercises grading; messaging behaves the same in the code.'
---
# Student of another group

## Given

A course is in forced separate groups mode, and the teacher is a member of one group only, without `moodle/site:accessallgroups`.

## When

the teacher asks to grade a student who belongs to the other group

## Then

nothing is saved and the error says that the student is not a student of this assignment
