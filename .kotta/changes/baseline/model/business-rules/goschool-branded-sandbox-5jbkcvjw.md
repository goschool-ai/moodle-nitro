---
id: BR-01m4v5j7bt3m6w1zws5jbkcvjw
form: business-rule
title: GoSchool-branded sandbox
capability: sandbox-onboarding
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/sandbox-onboarding/spec.md · Requirement: GoSchool-branded
      sandbox
    - 'PRODUCT.md · Brand Commitments (Brand by surface)'
    - 'PRODUCT.md · Stack; Product Principles 4'
    - 'openspec/changes/archive/2026-09-22-nitro-demo/tasks.md · 10.5'
    - 'deploy/branding.php'
  quote: >-
    The sandbox SHALL look like part of the GoSchool brand world, following the
    parent GoSchool design system (palette, typography, logo): the site name,
    logo, compact logo, favicon, brand colour and
---
# GoSchool-branded sandbox

## Rule

The sandbox SHALL look like part of the GoSchool brand world, following the parent GoSchool design system (palette, typography, logo): the site name, logo, compact logo, favicon, brand colour and login page SHALL be GoSchool's. Branding SHALL be applied to the sandbox only, through theme settings or a sandbox-only theme, and MUST NOT be part of `local_nitro` or change how nitro looks on a faculty Moodle.

## Rationale

The sandbox is GoSchool's own public practice site, where demo attendees sign up and try nitro; its landing and signup surfaces carry the goschool-web brand (PRODUCT.md, Brand by surface). nitro itself is Moodle-native and takes on each faculty's own theme, so the branding must stay out of `local_nitro` (PRODUCT.md, Stack and principle 4).

## Scope

The sandbox moodle.goschool.ai only: site name, logo, compact logo, favicon, the Boost brand colour and the login/signup page, set through core and theme settings by `deploy/branding.php`. It does not cover nitro's own pages on a faculty Moodle (consent screen, My connections, admin settings), where the faculty theme leads and the GoSchool logo appears only as a small, quiet mark.
