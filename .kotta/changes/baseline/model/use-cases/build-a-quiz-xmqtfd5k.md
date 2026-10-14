---
id: UC-01m4v5sydbccy34s28xmqtfd5k
form: use-case
title: "Build a quiz"
level: user-goal
actor:
  - A-01m4v5sv8ycsndkmh9kan23awt
goal:
  - G-01m4v5j7btwhrze40rpm7jc53c
refines:
  - BR-01m4v5j7bt1c9afnvsv8z4wpb4
  - BR-01m4v5j7btg9px95xnpj3jzeyy
  - BR-01m4v5j7btcd04kypy9wjaecwh
  - BR-01m4v5j7btkm0hyg6h2p15v1y5
  - BR-01m4v5za4v8sq9emc17gc9a180
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "local/nitro/classes/mcp/server.php · instructions"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D7. Questions through Moodle's importers"
  quote: "For a quiz: import the questions first (import_questions creates the category), then create_quiz (hidden), then add_questions_to_quiz"
  inferred: null
---
# Build a quiz

## Intent

The teacher gets a practice quiz built from course material: questions in the bank, a quiz that stays hidden until it is ready.

## Preconditions

The assistant is connected; the teacher can manage the course's question bank and quizzes; Moodle 5.0 or later.

## Main success scenario

1. The assistant reads the course material the teacher names (Check where the course stands).
2. It imports questions in GIFT or Moodle XML; the category is created on import.
3. It creates the quiz, hidden.
4. It adds fixed or random questions; the quiz is made visible only when the teacher wants it open.

## Alternatives

- The import has a parse error: nothing is imported and the error says where.
- There are fewer questions in the category than random slots: the call is refused.
- The quiz has attempts: its structure is not changed.
