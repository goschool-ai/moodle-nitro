---
id: EX-01m4v618mb0zr4p4r3f40nh2gn
form: example
title: Admin deletes a registered client
subjects:
  - BR-01m4v6110c7bjb2ac1t42wt5a1
  - UC-01m4v5szsa3p32es45jh0e8rvq
capability: oauth-server
provenance:
  level: partly-inferred
  decided_by: agent-decided
  sources:
    - "local/nitro/tests/behat/clients.feature · Register a confidential client, see its secret once, then delete it"
    - "local/nitro/classes/oauth/clients.php · delete"
  quote: null
  inferred: "Given, When and Then read from the cited test or code."
---
# Admin deletes a registered client

## Given

An admin registered the confidential client "Copilot at the faculty" on the OAuth clients page.

## When

the admin clicks Delete on its row and confirms

## Then

the page says the client was deleted, it no longer appears in the list, and any connection or token issued to it stops working
