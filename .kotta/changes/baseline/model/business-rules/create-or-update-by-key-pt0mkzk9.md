---
id: BR-01m4v5j7bt6hjy4k2jpt0mkzk9
form: business-rule
title: Create or update by key
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Create or update
      by key
    - "openspec/specs/content-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
    - "local/nitro/classes/local/modules.php · find_by_key, check_key"
    - "local/nitro/classes/external/create_quiz.php · execute"
    - "local/nitro/tests/external/save_page_test.php · test_first_publish, test_republish_updates_without_duplicate"
  quote: >-
    `save_page` and `save_assignment` SHALL take a `key` that identifies the
    activity within the course (stored as the activity's ID number).
  inferred: "The key length limit and create_quiz's handling of keys read from modules::check_key and create_quiz::execute."
---
# Create or update by key

## Rule

`save_page` and `save_assignment` SHALL take a `key` that identifies the activity within the course (stored as the activity's ID number). When no activity with that key exists, the tool SHALL create one; when it exists, the tool SHALL update it. The result SHALL state whether it created or updated, and return the module ID, key and URL. Calling the tool twice with the same arguments MUST NOT create a duplicate.

## Rationale

Republishing pages and assignments from the teacher's repository must be repeatable, as it was with the Canvas scripts that matched pages by title, but with a key that survives renaming. The course module ID number is used because every activity already has one, it is unique per course in practice, and the teacher can see and edit it in the web UI (D10).

## Scope

Applies to `save_page` and `save_assignment`: the key is 1 to 100 characters and is looked up among the course's activities that are not being deleted. `create_quiz` also takes an optional key but only creates and refuses a key already in use; other activity types have no create-or-update tool.
