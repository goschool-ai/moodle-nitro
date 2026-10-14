# Import the OpenSpec narrative into the technical model

## Why

The project's narrative specification under `openspec/specs/` is taken into the technical model through the planning phase, so that every node says whether its content was stated or inferred, and who decided it. The narrative is read, not changed.

## What

- Source: 12 capabilities, 58 requirements, 145 scenarios in `openspec/specs/`.
- Drafted by `kotta import openspec` into `model/`: 58 business rules (one per requirement), 145 examples (one per scenario, each proving its requirement's rule), 12 goals (one per capability Purpose). Every draft is `level: stated`, `decided_by: agent-decided`, its source naming the requirement it came from.
- No accepted node was matched; every draft is new. 215 drafts are new nodes.
- Copied, not written: the requirement text is the rule, the scenario steps are the example, the Purpose is the goal's outcome. Every section the narrative has no text for — a rule's rationale and scope, a goal's context, baseline and measure, an example's precondition where the scenario states none — is left empty with a note, for the planning phase to ask about.
- Not drafted: actors, use cases, entities and state machines. The narrative describes them only in passing; the planning phase derives them, and asks where it cannot.

## Told to a stranger

Nitro lets a university teacher run their Moodle course from the AI assistant they already use, such as Claude. The teacher pastes one address into the assistant, logs in with their Moodle account and approves the connection. From then on they ask in plain words: who is behind on the homework, write to each of them, make a practice quiz from this week's page, draft feedback on the submissions. Nitro does the work inside Moodle with exactly the teacher's permissions, and anything that reaches students (a message, an announcement, a grade) is shown to the teacher first and sent only after they approve it.

There are two ways in. A teacher can try it on GoSchool's trial Moodle, where signing up gives them their own course with 20 fictitious students. For real courses, the institution's Moodle administrator installs the plugin, decides who may use it and which tools are offered, and can switch it off at any time.

What supports it: Moodle itself is the server, so data goes straight between Moodle and the teacher's assistant with no service in between; every tool call is logged without its content; the teacher sees and revokes connections on their profile; every screen is in Hungarian and English.

This change reverse-engineers what is already built and running (plugin version 0.1.0, the trial Moodle open since 2026-10-07): the imported requirements, completed from the code and the archived nitro-demo design, plus the actors, journeys, entities and lifecycles the requirements imply.

## Capabilities

| Capability | Requirements | Scenarios | Purpose |
| --- | ---: | ---: | --- |
| `access-control` | 11 | 19 | yes |
| `audit-log` | 2 | 3 | yes |
| `content-authoring` | 6 | 10 | yes |
| `course-read` | 6 | 21 | yes |
| `feedback` | 2 | 6 | yes |
| `grading` | 2 | 5 | yes |
| `mcp-endpoint` | 5 | 15 | yes |
| `messaging` | 3 | 7 | yes |
| `oauth-server` | 10 | 30 | yes |
| `quiz-authoring` | 4 | 11 | yes |
| `sandbox-onboarding` | 4 | 9 | yes |
| `write-confirmation` | 3 | 9 | yes |

## History

1 archived changes record why the narrative says what it says. They are not imported; the planning phase reads them for the rationale a rule's text does not carry:

- `openspec/changes/archive/2026-09-22-nitro-demo/`

## Open decisions

- **Which purpose do the 12 goals serve?** The import drafted one goal per capability, side by side; the narrative names no goal above them. Name the product's purpose as a goal, and let each of these serve it (`serves`) — or say they stand on their own.
- **Which journey do the use cases form?** The narrative tells no order between capabilities. Name the steps a user goes through, in order, as a summary-level use case that includes them — or say there is none.

## Next

1. The `plan-change` skill: derive actors, use cases, entities and state machines from this proposal and the narrative; answer the sections marked not derivable from the sources, or ask.
2. `kotta plan baseline`, then the one human gate.
