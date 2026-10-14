---
id: G-01m4v5saj3ph52x7vag1y62efr
form: goal
title: "A teacher runs their Moodle week from their own AI assistant"
measured_by:
  - EX-01m4v5j7bt61m8xvj8n412qy9v
  - EX-01m4v5j7btd4c0fedkf83jmky6
  - EX-01m4v5j7btnzdk1dmahh89ypbz
  - EX-01m4v5j7bt0yk4y8732s8d91xz
serves: []
provenance:
  level: partly-inferred
  decided_by: agent-proposed-human-approved
  sources:
    - ".kotta/changes/baseline/conversation.md · J1"
    - "PRODUCT.md · Product Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "docs/index.html · hero"
  quote: "With nitro, a teacher can run their Moodle course from their own AI assistant ... Nothing reaches students without their approval."
  inferred: "That the target stays qualitative until the pilot is the product owner's choice (J1, answer 4)."
---
# A teacher runs their Moodle week from their own AI assistant

## Outcome

A teacher runs their Moodle course from their own AI assistant: reading the course, messaging students, writing content and quizzes, grading. It works with exactly the permissions the teacher has in the web UI, with no service in between, and nothing reaches students without the teacher's approval.

## Context

Moodle's core web services read well but cannot create a page, an assignment, a question or a quiz, and their tokens are copied by hand. The same workflow already works on Canvas through the Canvas API, where a programming course is run this way; institutions moving from Canvas to Moodle lose it. Nitro is part of the GoSchool product family: teacher work goes through MCP, in the teacher's own client.

## Baseline and target

Baseline: on Moodle a teacher cannot do this from an AI assistant today. Target: a teacher connects by pasting one URL and signing in, sees what they are allowing, can take it back at any time, and nothing reaches students without their approval. The target stays qualitative for what is built; the pilot change adds a measurable one.
