---
id: BR-01m4v5j7btg43hqqy6ypbhb7xa
form: business-rule
title: Markdown input with a cleaning report
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Markdown input
      with a cleaning report
    - "openspec/specs/content-authoring/spec.md · Purpose"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D6. Content writes through Moodle's module APIs"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D10. Stable keys for create-or-update"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 7.1"
    - "local/nitro/classes/local/content.php · from_markdown, removed"
    - "local/nitro/tests/local/content_test.php · test_headings_table_and_list, test_removed_content_is_reported"
  quote: >-
    Content tools SHALL accept text in markdown, convert it to HTML with
    Moodle's own markdown conversion, and store it after Moodle's normal text
    cleaning.
  inferred: "Which fields the rule covers read from save_page, save_assignment and create_quiz."
---
# Markdown input with a cleaning report

## Rule

Content tools SHALL accept text in markdown, convert it to HTML with Moodle's own markdown conversion, and store it after Moodle's normal text cleaning. When cleaning removes elements or attributes, the result SHALL list what was removed.

## Rationale

Teachers keep their material as markdown in their own repository, and content authoring must change or drop nothing silently. Moodle's own conversion and cleaning keep the stored HTML what the web UI would store, and listing what cleaning removed lets the teacher choose another form (D6, D10).

## Scope

Applies to the markdown fields of the writing tools: `save_page` content, `save_assignment` description and `create_quiz` introduction, stored as HTML. The report names each removed element or attribute with a count, also on a dry run; messages and announcements (messaging) are not covered.
