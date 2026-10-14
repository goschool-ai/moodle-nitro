---
id: EX-01m4v5zahrsyd931qpkq6g6nag
form: example
title: Key of an assignment reused for a page
subjects:
  - BR-01m4v5yvytezfyb4zmdxpp93dp
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/save_page_test.php · test_key_of_another_type"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Key of an assignment reused for a page

## Given

The course has an assignment with the key `hw2`; the teacher holds `local/nitro:use` and may manage activities.

## When

the teacher saves a page with the key `hw2`

## Then

nothing is saved and the error says the key belongs to an assign activity and asks for another key
