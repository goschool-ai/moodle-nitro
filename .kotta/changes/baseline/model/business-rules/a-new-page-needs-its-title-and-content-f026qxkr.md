---
id: BR-01m4v5z9jxx09fn5zbf026qxkr
form: business-rule
title: A new page needs its title and content
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "openspec/specs/content-authoring/spec.md · Purpose"
    - "local/nitro/classes/external/save_page.php · execute"
    - "local/nitro/classes/external/save_assignment.php · execute"
    - "local/nitro/tests/external/save_page_test.php · test_create_needs_name_and_content"
  inferred: "The obligation and its reason are read from the error messages in save_page::execute and save_assignment::execute and from the test; no spec states them."
---
# A new page needs its title and content

## Rule

When no page with the key exists, `save_page` MUST refuse a call without a title or without content and create nothing. When no assignment with the key exists, `save_assignment` MUST likewise refuse a call without a name.

## Rationale

Such a call would otherwise create an empty or untitled activity under a key the teacher may have mistyped; the error says that the call would create one, so the AI either adds the missing fields or checks the key.

## Scope

Applies to `save_page` and `save_assignment` when the key matches no activity in the course; on update every field may be omitted (see Partial updates).
