---
id: UC-01m4v5sym7ws6zscx731a5ksp5
form: use-case
title: "Grade submissions with feedback"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btebh7y87n2nngzv47
  - G-01m4v5j7btnqwnk44eqh7yqv9f
refines:
  - BR-01m4v5j7bt5phg37604exr0zrx
  - BR-01m4v5j7btsczj9z8dvzv0rtg9
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
  - BR-01m4v5j7bt18sj6vqww7fed3kb
  - BR-01m4v5xgmt4wajczbhkzvatfxf
  - BR-01m4v5xgwnjp2c6zd5dkkvxm16
  - BR-01m4v5xgej11sz0kb9pgv0qvwc
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D11. Grades and announcements through Moodle's own APIs"
  quote: "grade_submission uses the assign API ... so marking workflow, notifications and the gradebook behave as in the web UI"
  inferred: null
---
# Grade submissions with feedback

## Intent

The teacher grades submissions with an optional feedback comment, after seeing every grade in a preview.

## Preconditions

The assistant is connected; the teacher can grade the assignment.

## Main success scenario

1. The assistant reads the submissions (Check where the course stands) and proposes a grade and feedback per student.
2. The first `grade_submission` call returns a preview naming each student and grade.
3. The teacher approves; the repeated call saves the grades through the assign API, so marking workflow, notifications and the gradebook behave as in the web UI.

## Alternatives

- Marking workflow is on: grades are saved in the state the teacher asks for, by default not yet released to students.
- A grade is outside the scale or the maximum: the call is refused.
