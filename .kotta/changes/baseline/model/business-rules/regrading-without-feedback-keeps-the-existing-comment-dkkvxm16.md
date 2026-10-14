---
id: BR-01m4v5xgwnjp2c6zd5dkkvxm16
form: business-rule
title: Regrading without feedback keeps the existing comment
capability: grading
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'local/nitro/classes/external/grade_submission.php · current_comment()'
    - 'local/nitro/tests/external/grade_submission_test.php · test_half_points_with_feedback'
  inferred: 'Read from the code and the test.'
---
# Regrading without feedback keeps the existing comment

## Rule

When `grade_submission` saves a grade without a new feedback comment, the student's existing feedback comment SHALL be kept unchanged, as the web grading form keeps it. It MUST NOT be wiped.

## Rationale

Feedback is optional per student, so the AI often regrades without repeating the comment. The comments plugin always reads its editor, and without this rule an earlier comment the student may already have read would be erased.

## Scope

`grade_submission` on assignments with feedback comments switched on. Replacing a comment with a new text is still possible by giving one.
