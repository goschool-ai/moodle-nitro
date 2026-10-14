---
id: BR-01m4v5xh3edmajdb0n0mwp9dxj
form: business-rule
title: Only new confirmed accounts get one demo course
capability: sandbox-onboarding
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - 'openspec/changes/archive/2026-09-22-nitro-demo/design.md · D9 (a user preference marks it, so it happens once)'
    - 'local/nitrosandbox/classes/observer.php · maybe_queue()'
    - 'local/nitrosandbox/settings.php · enabled, onboardfrom'
    - 'deploy/sandbox.sh · --first (onboardfrom set to now; existing users are left alone)'
    - 'local/nitrosandbox/tests/provisioner_test.php · test_observer_queues_once_for_confirmed_users'
    - 'local/nitrosandbox/tests/provisioner_test.php · test_users_created_before_onboarding_get_nothing'
  inferred: 'The exclusions (unconfirmed, suspended, nologin, guest, admin) and the onboarding-start cut-off are read from the observer code.'
---
# Only new confirmed accounts get one demo course

## Rule

Onboarding SHALL queue at most one demo course per account. It SHALL do so only while onboarding is switched on, and only for confirmed accounts created after the moment onboarding started. Unconfirmed, suspended, `nologin`, guest and site administrator accounts MUST NOT get a demo course.

## Rationale

The sandbox existed before onboarding, and its existing users keep the site as it is (deploy script). Marking the account before the task runs means a second login before cron does not create a second course (D9).

## Scope

The sandbox onboarding observers on login and on account creation. The plugin is off by default, and the admin switches it on and sets the start moment. It does not cover the content of the course, which the provisioner builds.
