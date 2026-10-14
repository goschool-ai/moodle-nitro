---
id: BR-01m4v5j7bt5yy7xrsy53x8cxb1
form: business-rule
title: Confirmation for writes that reach students
capability: write-confirmation
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students
    - "openspec/specs/write-confirmation/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5"
    - "PRODUCT.md · Product Principles 2"
    - "local/nitro/classes/local/tools.php · tools::CONFIRM"
    - "local/nitro/classes/local/confirmation.php"
  quote: >-
    Tools that notify students or change what students see of their results (in
    this change: `message_students`, `post_announcement`, `grade_submission`)
    MUST NOT act on the first call.
---
# Confirmation for writes that reach students

## Rule

Tools that notify students or change what students see of their results (in this change: `message_students`, `post_announcement`, `grade_submission`) MUST NOT act on the first call. The first call SHALL return a preview and a confirmation token; the action SHALL be executed only when the tool is called again with the same arguments and that token.

## Rationale

Human approval is enforced on the server, so safety does not depend on the AI client following instructions, and the teacher always sees the text before it is sent (write-confirmation Purpose). It mirrors the teacher's own hard rule, "show it first, send only after an explicit go", which came from a message once sent without approval (design D5).

## Scope

`message_students`, `post_announcement` and `grade_submission`. `send_feedback` uses the same protocol, although it does not reach students. Content writes are excluded because they do not notify students (design D5). A preview is logged with outcome `preview`.
