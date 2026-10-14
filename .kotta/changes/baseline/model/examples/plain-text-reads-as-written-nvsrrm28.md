---
id: EX-01m4v5j7btzcysz9j7nvsrrm28
form: example
title: Plain text reads as written
subjects:
  - BR-01m4v5j7btbvevrsab4mp6a1py
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/messaging/spec.md · Requirement: Post announcement /
      Scenario: Plain text reads as written
    - 'local/nitro/tests/external/messaging_test.php · test_plain_text_keeps_the_lines_and_does_not_shout'
    - 'local/nitro/classes/local/content.php · to_plain()'
  quote: >-
    the message contains bold text and single line breaks, such as a two-line
    signature
  inferred: 'Given supplied from the test, which checks the plain-text preview of message_students; post_announcement uses the same conversion.'
---
# Plain text reads as written

## Given

The teacher previews a message to students written in markdown.

## When

the message contains bold text and single line breaks, such as a two-line signature

## Then

the plain text version keeps the words as written and the line breaks, and does not upper-case the bold parts
