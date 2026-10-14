---
id: BR-01m4v5z9ywbqaw6d4n303nmket
form: business-rule
title: Unreadable dates are refused
capability: content-authoring
provenance:
  level: inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/classes/local/dates.php · parse"
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "local/nitro/tests/local/content_test.php · test_bad_date, test_date_with_offset"
  inferred: "The obligation is read from dates::parse and its test; the roll-over reason is a code comment, the rest of the rationale is mine."
---
# Unreadable dates are refused

## Rule

Tools that take dates MUST refuse a value that is not an ISO 8601 date and time (a date without a time, a phrase such as "next friday", another date format, or a day that does not exist), change nothing, and name the parameter in the error with an example of a valid value.

## Rationale

A guessed date would put a deadline on the wrong day without anyone noticing; PHP would roll 2026-02-30 over to March, so an impossible day is refused instead of moved. The error says what to change, so the AI can ask the teacher or correct the value.

## Scope

Applies to `save_assignment` (`due`, `cutoff`) and `create_quiz` (`open`, `close`) through the shared date parser; an offset is optional, and `Z` or a space instead of `T` is accepted.
