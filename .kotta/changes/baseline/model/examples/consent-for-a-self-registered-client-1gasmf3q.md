---
id: EX-01m4v6190py1cewn9m1gasmf3q
form: example
title: Consent for a self-registered client
subjects:
  - BR-01m4v611cemfb81e81333p2bdv
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/oauth/authorize.php · selfregistered"
    - "local/nitro/templates/consent.mustache"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Consent for a self-registered client

## Given

A client registered itself through dynamic registration under the name "Copilot" with the Teams redirect URI, and a teacher with `local/nitro:use` is logged in.

## When

the teacher opens an authorization request from that client

## Then

the consent screen shows the name "Copilot", the redirect host `teams.microsoft.com`, and the warning that the application registered itself and its name is not verified
