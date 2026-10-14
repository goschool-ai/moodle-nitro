---
id: BR-01m4v5j7btn2q01dc6eskr8h9g
form: business-rule
title: Dry run for every write tool
capability: write-confirmation
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/write-confirmation/spec.md · Requirement: Dry run for every
      write tool
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D5 (content writes only get dry_run); D10 (no silent defaults); Risks (upsert overwrites manual edits)"
    - "local/nitro/classes/local/dry_run.php · dry_run::run"
    - "local/nitro/classes/mcp/server.php · server::INSTRUCTIONS"
  quote: Every tool that changes Moodle data SHALL accept a `dry_run` flag.
  inferred: "Scope: the list of tools that accept dry_run and the rollback mechanism, read from dry_run.php and the external functions."
---
# Dry run for every write tool

## Rule

Every tool that changes Moodle data SHALL accept a `dry_run` flag. With `dry_run` set, the tool MUST change nothing and SHALL return a description of what it would change, including the effective settings that would be stored.

## Rationale

An update by key can overwrite a teacher's manual edits in Moodle, so a dry run shows the changed fields first (design, Risks). A Canvas assignment created from chat once came out with no submission type and zero points, so results echo the effective settings (design D10). Content writes do not notify students, so they get a dry run and no confirmation (design D5).

## Scope

The content tools `save_page`, `save_assignment`, `import_questions`, `create_quiz` and `add_questions_to_quiz`. A dry run executes the write inside a database transaction that is always rolled back, so the result shows what Moodle would store, but records it would create get no ID or URL. Tools that reach students preview through the confirmation protocol instead.
