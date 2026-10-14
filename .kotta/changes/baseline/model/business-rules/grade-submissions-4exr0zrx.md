---
id: BR-01m4v5j7bt5phg37604exr0zrx
form: business-rule
title: Grade submissions
capability: grading
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/grading/spec.md · Requirement: Grade submissions'
    - 'openspec/specs/grading/spec.md · Purpose'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D11. Grades and announcements through Moodle''s own APIs'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens'
    - 'PRODUCT.md · Brand Commitments (grades are always decided by the teacher)'
    - 'local/nitro/classes/external/grade_submission.php · execute(), grade_value()'
  quote: >-
    `grade_submission` SHALL save grades for one or more students on one
    assignment.
---
# Grade submissions

## Rule

`grade_submission` SHALL save grades for one or more students on one assignment. A grade SHALL be a number within the assignment's maximum for point grading, or the name of a scale item (for example "Complete") for scale grading. An optional markdown feedback comment per student SHALL be saved as the assignment's feedback comment. It MUST require `mod/assign:grade` in the assignment's context and is subject to the confirmation protocol of `write-confirmation`.

## Rationale

The teacher records grades they have already decided on, such as accepting a batch of submissions or half points for late work, from the AI assistant; the AI never grades on its own (grading Purpose; PRODUCT.md). Grades go through the assign API, and a scale grade is given by its item name and mapped to the scale index (D11). A grade changes what students see of their results, so it waits for the teacher's approval (D5).

## Scope

`grade_submission` on one assignment, for students who are participants of that assignment and visible to the teacher. It covers point and scale grading; a feedback comment is accepted only when the assignment has feedback comments switched on. It does not cover automatic grading, extensions or feedback files.
