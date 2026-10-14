---
id: BR-01m4v5j7btsncgptdggxmhsz4e
form: business-rule
title: Independent of the nitro plugin
capability: sandbox-onboarding
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: Independent of
      the nitro plugin
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9. Sandbox onboarding as a separate plugin'
    - 'local/nitro/README.md · note on local_nitrosandbox'
    - 'PRODUCT.md · Capabilities and Constraints (the plugin creates no new roles)'
    - 'local/nitrosandbox/version.php · $plugin->dependencies'
  quote: Sandbox onboarding SHALL be installable and removable on its own.
---
# Independent of the nitro plugin

## Rule

Sandbox onboarding SHALL be installable and removable on its own. `local_nitro` MUST NOT depend on it, and faculty installations SHALL NOT need it.

## Rationale

Onboarding builds demo courses with fictitious students for the GoSchool practice site only, and an institutional Moodle must not get it (local/nitro/README.md). D9 therefore puts it in a separate plugin installed only on the sandbox; this also keeps the sandbox-only `nitrodemo` role out of `local_nitro`, which creates no roles.

## Scope

The dependency between the two plugins: `local_nitrosandbox` depends on `local_nitro`, never the other way round. It applies to every faculty installation and to the sandbox deployment, which copies both plugins. It does not cover the shared source repository.
