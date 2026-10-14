---
id: BR-01m4v5j7bt9rfe2s8t3a1jgyv3
form: business-rule
title: Save page
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/content-authoring/spec.md · Requirement: Save page'
    - "PRODUCT.md · Operating Context"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Why"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D6. Content writes through Moodle's module APIs"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · Non-goals"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 7.3"
    - "local/nitro/classes/external/save_page.php · execute"
    - "local/nitro/classes/local/modules.php · check_section, create"
    - "local/nitro/tests/external/save_page_test.php · test_first_publish, test_nonexistent_section, test_requires_manage_activities"
  quote: >-
    `save_page` SHALL create or update a Page activity with a title, markdown
    content, section and visibility.
  inferred: "That mod/page:addinstance is checked inside Moodle's create_module() (can_add_moduleinfo) and not by nitro's own code; creation defaults read from save_page::execute."
---
# Save page

## Rule

`save_page` SHALL create or update a Page activity with a title, markdown content, section and visibility. It MUST require `moodle/course:manageactivities`, and `mod/page:addinstance` when creating.

## Rationale

Pages republished from markdown in a repository are part of the teacher's weekly loop that nitro follows. The capability checks are those of the web UI; `mod/page:addinstance` is enforced by Moodle's own module creation, which nitro calls.

## Scope

Applies to `save_page`: a new page goes to section 0 and is visible unless the call says otherwise, and a section number that does not exist is refused with the highest existing one. Page display options keep Moodle's defaults; uploading files or images is not covered (deferred to `nitro-pilot`).
