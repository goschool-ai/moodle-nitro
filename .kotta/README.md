# Kotta workspace

This directory holds the repository's **technical specification**: the accepted promises of the
product in the precise, machine-checkable shape their forms declare. It is workspace shape
version 6.

- `spec/forms/` is the data-driven form registry. Every form's `directory` is relative to `spec/`,
  so nodes live in paths such as `spec/goals/`, `spec/use-cases/` and `spec/business-rules/`. The
  registry is the project's: add a form, and its nodes participate with nothing compiled in.
- `spec/<directory>/` holds the nodes, one Markdown file each, with the id, form, title, required
  fields and edges in the frontmatter. Identifiers are minted with `kotta spec new`, never typed.
  Every form also takes two optional fields: `capability` (a path such as `identity/user-auth`,
  grouping the nodes of one capability) and `provenance` —
  `level` (stated, partly-inferred, inferred), `decided_by` (human, agent-proposed-human-approved,
  agent-decided), `sources`, a `quote` of at most 30 words, and what was `inferred`. It is measured wherever present
  (`schemas/provenance.schema.json`) and required on every node a change proposes under
  `changes/<name>/model/`.
- `changes/<name>/` is a change: every proposal starts here, opened with `kotta change new <name>`.
  It holds `proposal.md` (why, what changes, what is still
  undecided), the distilled `conversation.md`, the model delta under `model/`, `kotta plan`'s
  `planning.md` and, after the human's yes, `approval.yaml`. `kotta archive` lands the delta in
  `spec/` and moves the change to `changes/archive/`.
- `AGENTS.md` is the rules file Kotta writes for the agents working in this project; `kotta sync`
  keeps it current and reports a hand-edited copy as drifted rather than replacing it.
- `config.yaml` names the project, the base branch and the protected branches.

A node becomes the agreement when it lands on the base branch on a human yes; `kotta validate` measures
every node against its form, and `kotta gap` reports which accepted promises the code keeps — by
naming the node's id where it keeps them — and which it does not.

Repository files are canonical. Chat, the board (`kotta ui`) and pull requests are views.
