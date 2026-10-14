---
id: EX-01m4v5j7btwjy3r8wc5252a44f
form: example
title: Moodle 4.5 site
subjects:
  - BR-01m4v5j7btkm0hyg6h2p15v1y5
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/quiz-authoring/spec.md · Requirement: Supported Moodle
      versions / Scenario: Moodle 4.5 site
    - "local/nitro/tests/external/quiz_tools_test.php · test_moodle_before_5"
  quote: '`import_questions` is called on a Moodle 4.5 site'
  inferred: "Given taken from the setup of quiz_tools_test.php::test_moodle_before_5."
---
# Moodle 4.5 site

## Given

nitro is installed on a site running Moodle 4.5.

## When

`import_questions` is called on a Moodle 4.5 site

## Then

it fails with an error saying Moodle 5.0 or later is required
