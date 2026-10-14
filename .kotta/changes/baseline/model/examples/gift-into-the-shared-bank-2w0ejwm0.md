---
id: EX-01m4v5j7btkx1yz0ke2w0ejwm0
form: example
title: GIFT into the shared bank
subjects:
  - BR-01m4v5j7bt1c9afnvsv8z4wpb4
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Import questions /
      Scenario: GIFT into the shared bank
    - "local/nitro/tests/external/quiz_tools_test.php · test_gift_into_new_shared_bank"
  quote: >-
    a teacher imports 15 multiple-choice questions in GIFT into category "Week
    4" of the course's shared bank
  inferred: "Given taken from the setup of quiz_tools_test.php::test_gift_into_new_shared_bank."
---
# GIFT into the shared bank

## Given

A Moodle 5.0 or later site; a teacher holding `local/nitro:use` and `moodle/question:add` in a course whose shared question bank has no category called "Week 4" yet.

## When

a teacher imports 15 multiple-choice questions in GIFT into category "Week 4" of the course's shared bank

## Then

15 questions exist in that category and the result lists their IDs
