---
id: BR-01m4v5j7bt8fgb45bv3ex3br5x
form: business-rule
title: Send feedback about nitro
capability: feedback
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - 'openspec/specs/feedback/spec.md · Requirement: Send feedback about nitro'
    - 'openspec/specs/feedback/spec.md · Purpose'
    - 'local/nitro/README.md · 4. Settings (Let teachers send feedback, Feedback address)'
    - 'PRODUCT.md · Product Purpose (data never goes through GoSchool)'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · Goals (no external runtime dependency)'
    - 'local/nitro/classes/external/send_feedback.php · execute()'
    - 'local/nitro/settings.php · feedbackenabled, feedbackemail'
  quote: >-
    `send_feedback` SHALL send a message written by the AI or the teacher to the
    address the admin configured (by default the nitro team's address), through
    Moodle's own mail.
---
# Send feedback about nitro

## Rule

`send_feedback` SHALL send a message written by the AI or the teacher to the address the admin configured (by default the nitro team's address), through Moodle's own mail. It SHALL carry the text, the site's name and URL, the nitro version, the Moodle version, and optionally the tool the feedback is about; it MUST NOT carry course content, student names or student data. The site admin SHALL be able to change the address or switch the tool off.

## Rationale

The teacher, through the AI, should be able to tell the nitro team what is missing or broken from inside the work, without leaving Moodle (feedback Purpose). The mail carries no course or student data because nitro's promise is that course data goes only between Moodle and the teacher's own AI client, never through GoSchool (PRODUCT.md). The admin can put in an institutional address to keep feedback inside the institution, or switch the tool off (README).

## Scope

The `send_feedback` tool, for users who pass nitro's access gate, with a text of 1 to 5000 characters. The settings are *Let teachers send feedback* (on by default) and *Feedback address* (default `info@goschool.ai`); when the tool is off it is also left out of the tool list. nitro adds only the site and version lines. The text itself comes from the AI and the teacher and is not filtered.
