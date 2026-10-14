---
id: BR-01m4v5j7bt63kng1skzff30aq7
form: business-rule
title: No third-party data flow
capability: access-control
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/access-control/spec.md · Requirement: No third-party data
      flow
    - "PRODUCT.md · Product Purpose; Positioning"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D3 (CIMD fetch); Risks (CIMD fetch used for SSRF); Security review (token endpoint never fetches)"
    - "local/nitro/classes/oauth/cimd.php · cimd::resolve"
    - "local/nitro/classes/local/mailer.php"
  quote: >-
    The plugin MUST NOT send course or user data anywhere other than back to the
    requesting client in the response, and MUST NOT store student data outside
    Moodle.
---
# No third-party data flow

## Rule

The plugin MUST NOT send course or user data anywhere other than back to the requesting client in the response, and MUST NOT store student data outside Moodle. The plugin makes two kinds of outbound request, neither carrying course or student data: fetches of client metadata documents from allowed hosts, and the feedback message of `send_feedback`, which the teacher approved and which carries only their text and the site and version information.

## Rationale

There is no service in between: data goes straight between Moodle and the teacher's own AI client, never through GoSchool. A hosted connector would carry student data through a third company on top of the AI client the institution already chose (PRODUCT.md, Purpose and Positioning).

## Scope

All of the plugin's code. Tool calls make no outbound request. Client metadata documents are fetched only from the CIMD host list, through Moodle's HTTP client, without following redirects, with a short timeout and a size cap, and never from the token endpoint. The feedback mail goes through Moodle's own mail. What the connected client does with a response is covered by the consent statement, not by this rule.
