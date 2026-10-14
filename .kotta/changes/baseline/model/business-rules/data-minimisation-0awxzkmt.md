---
id: BR-01m4v5j7bteex1ptp70awxzkmt
form: business-rule
title: Data minimisation
capability: access-control
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/specs/access-control/spec.md · Requirement: Data minimisation'
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · Risks (course and student data in the AI tool's conversation history)"
    - "openspec/changes/archive/2026-09-22-nitro-demo/proposal.md · What Changes; Non-goals"
    - "local/nitro/classes/external/list_participants.php · include_identity"
    - "local/nitro/classes/external/list_submissions.php · include_content"
  quote: Read tools SHALL return only the fields needed for course work by default.
  inferred: "Scope: that 'allowed to see' means the site's user identity fields for this user (core_user\\fields::get_identity_fields), read from list_participants.php."
---
# Data minimisation

## Rule

Read tools SHALL return only the fields needed for course work by default. Email addresses and identity fields (such as a student ID in `idnumber`) SHALL be returned only when the call explicitly asks for them and the user is allowed to see them in the web UI.

## Rationale

What the AI reads lands in the AI tool's conversation history, outside Moodle's reach, so default reads leave out identity fields and submission content (design, Risks). The proposal counts data minimisation among the things the plugin itself must do for an institution's data protection.

## Scope

Read tools, chiefly `list_participants` (email and ID number only with `include_identity`, and only those of the site's identity fields this user may see) and `list_submissions` (submitted text, links and file names only with `include_content`). It limits what a tool returns by default. It does not limit what the teacher may see in the web UI.
