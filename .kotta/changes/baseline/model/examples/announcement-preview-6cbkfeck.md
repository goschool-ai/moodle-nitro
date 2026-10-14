---
id: EX-01m4v5j7btc2v5ntw36cbkfeck
form: example
title: Announcement preview
subjects:
  - BR-01m4v5j7bt5yy7xrsy53x8cxb1
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Confirmation for
      writes that reach students / Scenario: Announcement preview
    - "local/nitro/tests/external/messaging_test.php · test_announcement"
  quote: a teacher calls `post_announcement` without a confirmation token
  inferred: "Given: read from the messaging test."
---
# Announcement preview

## Given

A teacher holds `local/nitro:use` and `mod/forum:addnews` in a course with enrolled students.

## When

a teacher calls `post_announcement` without a confirmation token

## Then

nothing is posted, and the result contains the subject, the rendered text, how many users would be notified, and a confirmation token
