---
id: EX-01m4v5zaygftk407jv2cdvscjf
form: example
title: Page without content
subjects:
  - BR-01m4v5z9jxx09fn5zbf026qxkr
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/external/save_page_test.php · test_create_needs_name_and_content"
  inferred: "The case is read from the test named in sources; no spec scenario states it."
---
# Page without content

## Given

No activity with the given key exists in the course; the teacher holds `local/nitro:use` and may manage activities.

## When

the teacher calls `save_page` with that key and content but no title

## Then

nothing is created and the error says that name and content are required, because the call would create a page
