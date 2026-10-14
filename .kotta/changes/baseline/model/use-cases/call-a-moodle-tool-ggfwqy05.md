---
id: UC-01m4v5t0c3b5wt6eggggfwqy05
form: use-case
title: "Call a Moodle tool"
level: subfunction
actor:
  - A-01m4v5svqwf7p6qmqf4gj4dmhq
goal:
  - G-01m4v5j7bte85hyxac31g4x4n1
refines:
  - BR-01m4v5j7bt31c41e3fpbthvp6m
  - BR-01m4v5j7btrczj6vmg8rdw60gx
  - BR-01m4v5j7btv9nytfppj0ebv4mt
  - BR-01m4v5j7btwm1zpvjmn90fwynp
  - BR-01m4v5j7btyg4dkzvag2p0g170
  - BR-01m4v5j7bt8723rb1rgvkrcn29
  - BR-01m4v5j7bt63kng1skzff30aq7
  - BR-01m4v5j7btn2q01dc6eskr8h9g
  - BR-01m4v6127a9gy8r5gvv9dbcb6p
  - BR-01m4v611rpjb0y1hnprnwvsc7w
  - BR-01m4v61205akg1k1wb0btt3wym
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D1. Architecture: Moodle is the MCP server"
    - "openspec/changes/archive/2026-09-22-nitro-demo/design.md · D2. Own MCP layer"
  quote: "Each tool is a Moodle external function declared in db/services.php, starting with validate_context() and require_capability()."
  inferred: null
---
# Call a Moodle tool

## Intent

The assistant calls one Moodle tool over MCP as the teacher who owns the token, under every guard the web UI has.

## Preconditions

The assistant holds a valid access token of a teacher with `local/nitro:use`.

## Main success scenario

1. The assistant sends a JSON-RPC `tools/call` to `/local/nitro/mcp.php` with the bearer token.
2. Nitro checks the kill switch, the token and the tool allowlist, and sets the token's owner as the user.
3. The tool validates its arguments and checks the course context and the capability the same action needs in the web UI.
4. It runs, returning only the fields the call needs; a write tool with `dry_run` changes nothing.
5. A tool call record is written to the Moodle log; the result goes straight back to the assistant, with no third party in between.

## Alternatives

- No or bad token: 401 with the discovery pointer.
- Unknown or removed tool, invalid arguments or a missing permission: an error result that says what to change.
- A notification without id: 202 and no body.
