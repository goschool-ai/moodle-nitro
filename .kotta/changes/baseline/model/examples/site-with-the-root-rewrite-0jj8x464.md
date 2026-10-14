---
id: EX-01m4v5j7bt7w7gs17k0jj8x464
form: example
title: Site with the root rewrite
subjects:
  - BR-01m4v5j7btjrh80q3a7z21xadp
provenance:
  level: stated
  decided_by: agent-decided
  sources:
    - >-
      openspec/specs/oauth-server/spec.md · Requirement: Discovery metadata /
      Scenario: Site with the root rewrite
    - "local/nitro/README.md · 3. Web server (Root discovery URLs)"
    - "local/nitro/tests/oauth/metadata_test.php · test_for_path"
  quote: the site's web server maps the root well-known paths to the plugin
---
# Site with the root rewrite

## Given

The admin added the README's rewrite rules, which map the root `/.well-known/oauth-protected-resource`, `/.well-known/oauth-authorization-server` and `/.well-known/openid-configuration` paths to the plugin's metadata script.

## When

the site's web server maps the root well-known paths to the plugin

## Then

`/.well-known/oauth-protected-resource` and `/.well-known/oauth-authorization-server` return the same documents
