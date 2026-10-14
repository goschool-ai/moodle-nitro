---
id: BR-01m4v5j7bt812y1xxt7wwqq2w5
form: business-rule
title: List assignment submissions
capability: course-read
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/course-read/spec.md · Requirement: List assignment
      submissions
    - "PRODUCT.md · Operating Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "openspec/specs/course-read/spec.md · Scenario: Graded students stay in the list"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks / Trade-offs (Course and student data lands in the AI tool's conversation history)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Security review (separate groups mode)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 5.4"
    - "local/nitro/classes/external/list_submissions.php · execute"
    - "local/nitro/tests/external/list_submissions_test.php · test_all_students_with_state"
  quote: >-
    `list_submissions` SHALL return, for an assignment, every student's
    submission status (not submitted, draft, submitted), submission time,
    whether it was late, any granted extension, whether it has been graded, and
    the
  inferred: "Counts, the overdue flag and the group filter read from list_submissions::execute."
---
# List assignment submissions

## Rule

`list_submissions` SHALL return, for an assignment, every student's submission status (not submitted, draft, submitted), submission time, whether it was late, any granted extension, whether it has been graded, and the current grade. With `include_content` set, it SHALL also return the online text of the submission (links in it listed separately) and the names and sizes of submitted files. It SHALL support filtering to missing, late, ungraded or graded submissions. It MUST require `mod/assign:grade` in the assignment's context.

## Rationale

The teacher's weekly loop starts with "who is behind?" and goes on to accepting submissions, so every student's state comes in one call, and graded students stay in the list so a status check never loses accepted work. Submitted text, links and file names are returned only on request, because course data lands in the AI tool's conversation history and default reads leave submission content out.

## Scope

Applies to `list_submissions` for one assignment, for a user holding `local/nitro:use` and `mod/assign:grade` there; in separate groups mode a user without `moodle/site:accessallgroups` sees only their own groups' students. The result also carries counts and an overdue flag and can be narrowed to one group; file contents are never returned, and grading belongs to `grade_submission`.
