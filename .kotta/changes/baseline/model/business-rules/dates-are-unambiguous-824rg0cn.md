---
id: BR-01m4v5j7btmzq4qn0n824rg0cn
form: business-rule
title: Dates are unambiguous
capability: content-authoring
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/content-authoring/spec.md · Requirement: Dates are
      unambiguous
    - "local/nitro/classes/mcp/server.php · INSTRUCTIONS"
    - "openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 7.1"
    - "local/nitro/classes/local/dates.php · parse, describe, describe_returns"
    - "local/nitro/tests/local/content_test.php · test_date_without_offset_uses_user_time_zone"
  quote: >-
    Tools that take dates SHALL accept ISO 8601 date-times and SHALL interpret
    values without a time zone in the user's Moodle time zone.
  inferred: "That the weekday serves a read-back check is taken from the field description in dates::describe_returns and the server instructions; which tools take dates read from the code."
---
# Dates are unambiguous

## Rule

Tools that take dates SHALL accept ISO 8601 date-times and SHALL interpret values without a time zone in the user's Moodle time zone. Results SHALL echo dates in ISO 8601 with offset, together with the weekday.

## Rationale

The server instructions tell the AI to tell the teacher the weekday and date it is about to use, and results carry the weekday so the date can be read back to the teacher before it is relied on. A time without an offset is taken in the teacher's own Moodle time zone, so a deadline means what the teacher means and not the server's or the AI's time zone.

## Scope

Applies to the tools that take dates, `save_assignment` (`due`, `cutoff`) and `create_quiz` (`open`, `close`), whose results echo each date with its offset and weekday. The read tools return ISO 8601 with offset but no weekday; a value that is not a full date and time is refused (see Unreadable dates are refused).
