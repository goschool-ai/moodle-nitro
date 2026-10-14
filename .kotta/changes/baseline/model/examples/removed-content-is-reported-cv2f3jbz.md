---
id: EX-01m4v5j7btpeepp7cccv2f3jbz
form: example
title: Removed content is reported
subjects:
  - BR-01m4v5j7btg43hqqy6ypbhb7xa
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Markdown input
      with a cleaning report / Scenario: Removed content is reported
    - "local/nitro/tests/local/content_test.php · test_removed_content_is_reported"
  quote: >-
    the markdown contains an inline `<svg>` or `<script>` element that Moodle's
    cleaning removes
  inferred: "Given taken from the setup of content_test.php::test_removed_content_is_reported."
---
# Removed content is reported

## Given

A teacher who holds `local/nitro:use` and may manage activities saves content written in markdown; Moodle's HTML cleaning is on, as it always is.

## When

the markdown contains an inline `<svg>` or `<script>` element that Moodle's cleaning removes

## Then

the content is saved without it, and the result names the removed element so the teacher can choose another form
