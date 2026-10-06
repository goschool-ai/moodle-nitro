# Advanced Programming on the sandbox: publish plan

A showcase course built with nitro from the teacher's own sources. It mirrors the real Canvas course
(canvas.elte.hu, course 66074) as of 2026-10-02.

- Moodle course: `advprog`, id 163, on https://moodle.goschool.ai, created empty with named sections.
  Teacher: `arpad.tamasi`.
- Sources: `~/Dev/phd/oktatas/advancedprogramming` (`pages/`, `assignments/`, `example/README.md`).
- Not published: `pages/projektek.md` and `hallgatok.md` (real students' names and projects).

## Status

Published through nitro on 2026-10-02: every page, the five assignments and the quiz (8 questions,
visible). The teacher has the `nitrodemo` role in the course, which grants `local/nitro:use`.
Not done: the welcome announcement (waits for the teacher's approval of the preview).

Course module IDs: `start-here` 810, `requirements` 816, `git` 821, quiz 825, `session-1` 811,
`hw1` 817, `example-readme` 822, `session-2` 812, `hw2a` 818, `hw2b` 823, `session-3` 813, `hw3` 819,
`week-4` 814, `hw4` 820, `session-4` 815.

Changes against the source: Homework 3's "Stuck?" no longer links a forum (nitro cannot create one);
the git page describes the three diagrams in words and a table.

## Order of work

1. `list_courses`, then `course_overview` for course 163.
2. Pages, first pass (content with links to external sites only).
3. Assignments.
4. Question bank, quiz, questions.
5. Pages, second pass: replace the Canvas links with the Moodle links of the activities created above
   (`/mod/page/view.php?id=<cmid>`, `/mod/assign/view.php?id=<cmid>`). Same keys, so nothing duplicates.
6. Welcome announcement: preview, then the teacher's approval.

## Activities

| Section | Type | Key | Name | Source |
|---|---|---|---|---|
| 0 | page | `start-here` | Start here | `pages/kezdolap.md`, "What to do now" rewritten for Homework 4 |
| 0 | page | `requirements` | Requirements and grading | `pages/requirements.md` |
| 0 | page | `git` | Git — what you need for this course | `pages/git.md`, see "Images" below |
| 0 | quiz | `check-git-rhythm` | Check yourself: git and the weekly rhythm | questions below |
| 1 | page | `session-1` | Session 1 — what we did | below |
| 1 | assignment | `hw1` | Homework 1 — Repository and project brief | `assignments/01-projektinditas.md` |
| 1 | page | `example-readme` | Example README.md | `example/README.md` and the Canvas page `example-readme-dot-md` |
| 2 | page | `session-2` | Session 2 — what we did | below |
| 2 | assignment | `hw2a` | Homework 2a — Snake | `assignments/02a-snake.md` |
| 2 | assignment | `hw2b` | Homework 2b — Cassino | `assignments/02b-cassino.md` |
| 3 | page | `session-3` | Session 3 — what we did | below |
| 3 | assignment | `hw3` | Homework 3 — Your MVP, with OpenSpec | `assignments/03-openspec-mvp.md` |
| 4 | page | `week-4` | No session this week | below |
| 4 | assignment | `hw4` | Homework 4 — Your spike | `assignments/04-spike.md` |
| 5 | page | `session-4` | Session 4 — bring your number | below |

## Assignment settings

All: `submission_types: ["onlinetext"]` (the student pastes a repository URL), `max_points: 1`.
Times are Europe/Budapest.

| Key | Due | Cut-off |
|---|---|---|
| `hw1` | 2026-09-14T20:00 (Monday) | none |
| `hw2a`, `hw2b` | 2026-09-18T20:00 (Friday) | 2026-09-21T20:00 (Monday) |
| `hw3` | 2026-09-26T20:00 (Saturday) | 2026-09-28T20:00 (Monday) |
| `hw4` | 2026-10-03T20:00 (Saturday) | 2026-10-05T20:00 (Monday) |

**Homework 4 dates differ from the source.** The source and the Canvas announcement say "Saturday
4 October" and "Monday 6 October", but 4 October 2026 is a Sunday and 6 October is a Tuesday. The
teacher decided on 2026-10-02 that Saturday is meant, so the due date is 3 October; the cut-off follows
the weekly rule (Monday, 5 October). In the description, write "Saturday 3 October" and "Monday
5 October". Canvas still has Sunday 4 October and Tuesday 6 October.

## Images

`pages/git.md` shows three diagrams from Canvas file URLs that need a Canvas login. nitro has no file
upload, so the page is published without them unless the PNGs in `pages/kepek/` get a public URL
(for example on esst-prog2.github.io).

## Session pages

### `session-1` — Session 1 — what we did

The course started. You take a Python project of your own from an idea to a reproducible release, and
this week it is still an idea.

**Before the next session**

- Create your project repository on GitHub and write its `README.md`: the project brief. That is
  Homework 1.
- Start with the demo section. It is the one that makes everything after it concrete.
- No code yet. The work this week is writing the brief: that is where you find out which parts of your
  idea are not decided.

Pick something you actually want to use. You will spend twelve weeks with it.

Push what you have by Friday morning and you get feedback the same day, so the weekend is for working
on the feedback. Slides: [esst-prog2.github.io](https://esst-prog2.github.io).

### `session-2` — Session 2 — what we did

Two parts.

**1. The development environment, on your own laptop.** VS Code, a coding agent inside it (Claude Code
or Codex), and your project on your machine: you ask the agent to clone your repository from
esst-prog2, and it does the rest.

"Works" means: your project folder is open in VS Code, you ask the agent "What is this project?", and
it answers from your README.

**2. Vibe coding versus agentic engineering.** What the difference is, and why it matters for your
project. This week's homework is vibe coding, on purpose: prompts only, tell the agent what is wrong
in words, and play what you built.

**This week:** Homework 2a (Snake) and Homework 2b (Cassino). Both are forks; submit the URL of each.

### `session-3` — Session 3 — what we did

From your brief to a specification, and from the specification to working code. For the first time
the agent works from a spec, on your own project.

The steps, in the order the homework asks for them:

1. **explore**: carry on the conversation about your README until it offers to write the change request
2. **propose**: let it write the change request for your MVP
3. **apply**: let the agent build it
4. **commit and push**: what is not pushed does not exist

Missed the session? The slides show every step, including the exact lines to paste for the planning
log and for installing OpenSpec. Start at "Your turn": [esst-prog2.github.io](https://esst-prog2.github.io).

**From this week homework is due Saturday 20:00**, not Friday.

### `week-4` — No session this week

The session on 29 September is cancelled. We meet again on 6 October, with the session on technical
exploration.

There is no new material this week. Instead, each of you gets a **spike**: the smallest piece of work
whose output is not a feature but an answer. You are not building this week; you are finding out
whether something you already believe is true.

Your own spike, the one question your project most needs answered, is an issue in your repository
called **Your spike**. Homework 4 says how to start it on a branch, what counts as evidence, how to
hand it in through a pull request, and how it is checked.

If you did not hand in Homework 3, that is your homework instead.

### `session-4` — Session 4 — bring your number

This session is technical exploration, and it is built on what your spikes found.

Come with your number: the question you asked, what you did, and what came out.

## Quiz: `check-git-rhythm`

Practice quiz, unlimited attempts, answers shown after each attempt, no time limit, 1 mark each.
Category: `Git and the weekly rhythm`. Every answer is on the Git page or on Requirements and grading.

```gift
::Lost in git::When you are lost in git, which command do you run first? {
=git status #It answers "where am I?": what is changed, what is staged, and whether you are ahead of the remote.
~git push #Pushing sends commits; it does not tell you where you are.
~git clone #You clone once, at the start.
~git restore #That undoes a change in a file; first find out what changed.
}

::Four places::Git keeps your work in four places. Which command moves it from your local repository to GitHub? {
=git push #Push sends your commits to the remote on GitHub.
~git add #Add moves changes from the working tree to the staging area.
~git commit #Commit moves staged changes into your local repository.
~git clone #Clone brings a copy down once, at the start.
}

::Not pushed::Work that is committed on your laptop but not pushed counts as handed in. {FALSE #What is not pushed does not exist: the instructor opens the repository on GitHub.}

::Rejected push::You push and see "! [rejected] ... fetch first". What do you do? {
=Run git pull --rebase, then git push #The copy on GitHub has a commit yours does not.
~Delete the repository and start again #Nothing is lost; you only need the missing commit.
~Run git add -A again #Staging more changes does not bring down the commit you are missing.
~Wait and push again later #The remote will not change back on its own.
}

::Branches::For this course's weekly work you need to learn branching before you can start. {FALSE #You work on main. Homework 4 gives you the one branch command it needs.}

::Late homework::A homework is due Saturday 20:00. You submit it on Monday at 19:00. How much does it count? {
=Half #By Monday 20:00 it counts half.
~In full #In full only by Saturday 20:00.
~Not at all #Not at all only after Monday 20:00.
~It depends on the reason #A setup problem is not a reason to be late; bring it to the session before the deadline.
}

::Grade parts::Which part of the grade is scored by your classmates? {
=The final demo #Every student in the room is on the jury and scores every other demo.
~The homework #Every homework counts equally, and the instructor checks it.
~The instructor's assessment #That part is the instructor's own.
~None of them #The final demo is 40% of the grade, and your classmates are the jury.
}

::Using AI::The course's hint for working with AI has three steps. Which is the second? {
=You ask the AI for a second opinion #First you think about the problem, then you ask, then you decide.
~You ask the AI to solve the problem #You think about the problem first.
~You copy the answer into your project #You decide, and then move on.
~You ask a classmate #The hint is about how you and the AI share the thinking.
}
```

## Welcome announcement

Subject: `Welcome — your first task`. Text: the Canvas announcement of 2026-09-08, with the course
materials link kept and "the course page" pointing at this Moodle course. It reaches students, so nitro
shows a preview first and posts only after the teacher approves.
