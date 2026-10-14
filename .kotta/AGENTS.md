# AGENTS.md

This repository keeps its **technical specification** with **Kotta**: the accepted rules, examples,
entities, state machines, use cases, stories and interfaces of the product, as plain Markdown files
under `.kotta/spec/`, each in the shape its form declares. Read this before you touch anything.

## The tool these rules assume

The rules below are enforced by the `kotta` CLI and by the Kotta MCP server. The binary and the npm
package do not share a name, so the package cannot be guessed from the command:

```bash
npm install --global @arpadtamasi/kotta@1.0.0-alpha.11   # or: npx -y -p @arpadtamasi/kotta@1.0.0-alpha.11 kotta validate
```

If you can install neither — a hosted environment with no network or no npm — read the
specification as files, draft your proposal in a file, say what you could not run, and leave the
checks to an environment that has the CLI.

## Where a change lives

```text
chat  →  change (proposal + model delta)  →  technical model (Kotta forms)  →  code
```

The layers are joined by references, never by copies. The conversation is where intent is said; a
**change** is where it is proposed, argued in prose and translated into nodes; the **technical
model** is the precise, machine-checkable form of what was accepted — and when a proposal and the
model disagree, the model is the truth and the disagreement is reported, not smoothed over; the code
is what keeps the promises, and it says which by naming the node it keeps.

**Every request to specify, propose or plan something starts as a change**, and a change has one
home: `.kotta/changes/<name>/`, opened with `kotta change new <name>`. The change holds `proposal.md` (why, what changes, what is still undecided, in prose), the
conversation that shaped it, distilled into `conversation.md` by `kotta narrative <name> --from
<session log>` (secrets and personal data filtered before anything is written), and the **model
delta** under `model/`, every node marked with its `provenance` (stated, partly inferred or inferred,
and who decided it). `kotta plan <name>` measures the delta against the accepted model and writes
`planning.md`; the human decides it — **the one gate** — and `kotta approve <name> --by <who>`
records that yes. The approved change then stays open while the code that keeps it is written:
`kotta gap` lists what is left of it. `kotta archive <name>` closes it — it lands exactly the
approved delta in `.kotta/spec/` and moves the change to `.kotta/changes/archive/`,
asking nothing again, and refusing only a node that is neither kept nor admitted. The `plan-change`
skill is the how. The order is plan, gate, build, archive, and the code never comes before the gate
(rule 6).

## The rule everything else follows from

`.kotta/spec/` is **project-owned**. Its form registry (`spec/forms/*.yaml`) and the nodes
stored in form-declared directories are yours to shape, in conversation, with the workshop skills or
by hand — the validator measures a node against its form and names the form's own question for
every missing part. A node becomes the agreement when it lands on the base branch on a human yes.

Kotta holds what was agreed and whether the code keeps it. The one receipt it writes is a change's
`approval.yaml`, the record of its one gate.

Chat, the board (`kotta ui`), pull requests and CI are views or history — they never override these
files. The board is read-only.

## Orient yourself first

```bash
kotta validate     # does every node satisfy its form, and every edge name a node
kotta gap          # which accepted promises have no evidence in the code, what is left to build of each approved open change, and which enforcement has no spec behind it
kotta questions    # which drafts still carry an open question
kotta change list  # which changes are open
```

Project settings — the base branch, the protected branches — live in `.kotta/config.yaml`.
Read it rather than assuming defaults.

## The model

Every node is one Markdown file with frontmatter: `id`, `form`, `title`, the fields its form
requires, and the edges it answers (a use case names its `actor` and `goal`; an example names what it
`subjects`). The registry says which forms exist, where their nodes live, which sections they must
carry and which edges they must answer. A form the project adds participates the same way, with
nothing compiled in.

Identifiers are minted, never typed: `kotta spec new <form> --title "…"` writes a node already
carrying its id and its form's skeleton, as a draft, committed by nobody. Name a node by its
**title** wherever a human reads; the id is a key for the machine and appears where something will be
typed back or nothing else identifies it.

**A promise is evidenced by citation.** The code, test or command definition that keeps a node names
that node's id, so `kotta gap` finds it in one pass. A node nothing names either has an admission in
its frontmatter — `accepted: ["<kind>: <reason>"]`, where the kind is `structural`, `unexamined` or
`unimplemented` — or the report refuses and names it. Keeping a promise without naming it leaves it
unaccounted for.

Undecided points in a draft are enumerated under `Open decisions`, one list item each, addressed as
`<id>/Q<n>` by their position; `kotta questions` lists them. The planning phase of a change is where
they are answered and where the one human gate stands: `kotta approve` refuses a delta with an open
question, so a draft with one is a draft, not an agreement.

## Rules for agents

1. **The technical model is the accepted truth.** When a proposal, the conversation and the
   nodes disagree, the nodes win and you say where they disagree. You may propose a change to a
   node; you do not decide one. The change lands when the human says yes. `kotta plan`'s conflict
   candidates help; they do not replace comparing every claim of a delta with the accepted nodes it
   touches, and what you find goes into the report marked `judged`. Between a change's approval and
   its archive, the approved delta is the agreement for the nodes it touches.
2. **Never invent product intent.** Where a form asks for something — a goal, an actor, a rationale —
   that neither the conversation nor the proposal says, write the question, not an answer. A
   filled-in guess is worse than a listed gap.
3. **Stay inside the change you are making.** Anything you notice outside it is said to the human,
   in chat, as one line — never a silent fix and never a record of its own.
4. **Approval is a human gate — ask for it here, in the conversation.** Put the decision to the
   human in their language: what will change, named by title, one line, then a plain yes or no.
   Anything less than an explicit yes is a no: silence, a yes to a different question, an earlier
   unrelated yes, or your own judgement that they would obviously agree. If you cannot ask, you do
   not decide. There is one such gate per change, at the end of planning, and none after it. Once
   the yes is recorded, offer the work the change describes as the next step, not the archive: the
   change stays open until it is built.
   Write every question for someone who has not read the code or the specification: say in plain
   words what the thing is, what it says today and why the answer matters, give the options and
   what each would mean, and mark your own suggestion as yours. No file names, no ids, no knowledge
   of the repository assumed.
5. **Evidence names its node.** When you implement a promise, cite the node's id where the code
   keeps it and in the test that proves it. A node of an approved change that is still open needs
   no admission while it is unbuilt — it is the work that remains. What is still unkept when the
   change is archived, you admit in the node's frontmatter with its kind and reason; `kotta archive`
   refuses a node that is neither kept nor admitted.
6. **The code never runs ahead of the spec.** Before you write code that keeps, changes or drops a
   promise that neither the accepted model nor an approved open change states, open a change for it
   and take it through `plan-change` to the gate; write the code after the human's yes. Say so in one
   line, naming the promise in plain words, instead of writing the code. Building an approved change
   needs no new change, and neither does work that touches no promise.
7. **Quality said is quality recorded.** When the conversation or a proposal says how well the
   product must do something — how readable, findable, fast, stable, accessible or calm — say that it
   is a quality requirement and draft it into the change as a quality attribute, with a response and
   a measure. Where nobody said the measure, ask for it under `Open decisions`; do not pick one
   silently. Never leave it as a build note in the proposal's prose: prose is history once the change
   is archived, nothing cites it, and nothing notices when it regresses. `kotta plan` lists every item
   of the proposal's What changes that names no node, for you to judge.
8. **Tell the product to a stranger first.** Before you shape a model, write under
   `## Told to a stranger` in the proposal, in a few sentences, what the product is for, how it is
   used step by step, its variants and what supports it. Then propose the model that reads the same
   way: a purpose the product's goals serve, each journey as a summary use case, variants as
   extensions; support that is a quality of the product — privacy, access, installation,
   timeliness — as a quality attribute, support someone does as a use case off the journey. A
   purpose, a journey or a measure the human has not said is a question under `Open decisions`,
   never your answer. `kotta plan` names a proposal that tells no stranger.

## Skills

If the Kotta skills are installed, prefer them — they encode the how. `plan-change` carries a
change from its proposal to the one gate; `setup-kotta` initializes a workspace; `explore-workspace` answers questions across the specification; `requirements-traceability`
reads the model as a graph and reports what hangs; `consolidate-model` finds one concept living under
several names; `report-kotta-bug` reports a defect in Kotta itself. `kotta sync` installs them.

For the specification workshops, use `impact-mapping`, `story-mapping`, `use-case-modeling`,
`example-mapping`, `event-storming`, `ubiquitous-language`, `quality-scenarios` and
`design-by-task`. They read the accepted nodes under `.kotta/spec/` and draft new ones into a
change (`kotta spec new <form> --title "…" --into <change>`), never straight into the accepted
specification: a drafted node reaches it through the change's one gate.

A defect in Kotta itself is not this project's work: use `report-kotta-bug`, or the issue form at
<https://github.com/arpadtamasi/kotta/issues>.

---

This file is written and kept current by Kotta. Edit it and `kotta sync` will report it as drifted
and leave it alone; project-specific instructions belong in the repository's own `AGENTS.md`, which
Kotta never rewrites.
