---
id: BR-01m4v5j7btsczj9z8dvzv0rtg9
form: business-rule
title: Grading follows the assignment's settings
capability: grading
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/grading/spec.md · Requirement: Grading follows the
      assignment's settings
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D11. Grades and announcements through Moodle''s own APIs'
    - 'PRODUCT.md · Product Principles 1 and 3'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 9.3'
    - 'local/nitro/classes/external/grade_submission.php · execute() (workflow state, visible_to_students, students_notified)'
  quote: >-
    Saved grades SHALL follow the assignment's existing settings as a grade
    saved in the web UI would: marking workflow, grade release and student
    notification.
---
# Grading follows the assignment's settings

## Rule

Saved grades SHALL follow the assignment's existing settings as a grade saved in the web UI would: marking workflow, grade release and student notification. The preview SHALL state whether students will be notified and whether the grade will be visible to them immediately.

## Rationale

Saving through the assign API means marking workflow, notifications and the gradebook behave as they do in the web UI (D11; principle 1). The preview says what students will see and when, so a cautious teacher knows what they are approving (principle 3).

## Scope

Every grade saved by `grade_submission`. With marking workflow on, grades are saved in the state the teacher chose (default `readyforrelease`, not visible to students); without it, a workflow state is refused. Students are notified only when the grade becomes visible and the assignment sends notifications. The tool never changes the assignment's own settings.
