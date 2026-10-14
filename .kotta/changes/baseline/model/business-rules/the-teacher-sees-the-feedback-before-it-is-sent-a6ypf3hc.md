---
id: BR-01m4v5j7btgddbv0vya6ypf3hc
form: business-rule
title: The teacher sees the feedback before it is sent
capability: feedback
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/feedback/spec.md · Requirement: The teacher sees the
      feedback before it is sent
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5. Server-side confirmation tokens'
    - 'local/nitro/README.md · 4. Settings (the teacher approves the exact text first)'
    - 'local/nitro/lang/en/local_nitro.php · feedbackenabled_desc'
    - 'local/nitro/classes/external/send_feedback.php · execute()'
  quote: >-
    `send_feedback` is subject to the confirmation protocol of
    `write-confirmation`: the first call SHALL return the exact message and a
    confirmation token, and it SHALL be sent only when the call
---
# The teacher sees the feedback before it is sent

## Rule

`send_feedback` is subject to the confirmation protocol of `write-confirmation`: the first call SHALL return the exact message and a confirmation token, and it SHALL be sent only when the call is repeated with that token.

## Rationale

D5's hard rule, "show it first, send only after an explicit go", came from a message sent without approval. Feedback leaves the site for an outside address, so the teacher approves the exact text before it goes (README; settings description).

## Scope

`send_feedback` only. The preview returns the recipient, the subject and the exact message body with a token bound to the user, the tool and the arguments. A queued retry after a mail failure resends the approved message without asking again.
