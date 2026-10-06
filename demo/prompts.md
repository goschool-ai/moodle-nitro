# Demo prompts – BME Oktatói Klub, 2026-10-06

Every run starts from a fresh demo course: sign up a new account on the sandbox, wait for "Demo NN – Haladó programozás", then connect Claude to `https://moodle.goschool.ai/local/nitro/mcp.php`. Messages, grades and announcements change the course, so a course is good for one run only. For the stage, sign up the account the day before (task 11.4) and do not touch its course afterwards: the seeded dates follow the signup day, so a course made more than two days early has its final deadline behind it.

The prompts name no tools. Claude has to find the way from the server instructions alone (task 11.1). If it needs an extra hint, note which prompt and what it got wrong.

## The shape of the demo

The talk hands the demo two debts, and the order below pays them in order:

1. Slide 6 says the teacher made a pedagogical decision. **Part 1** shows the
   decision reaching Moodle.
2. Slide 7 says that decision costs 176 judgements and 44 individual
   replies. **Part 2** shows that cost being paid at class scale.

Part 1 alone is a one-student demo and does not settle the second debt. If
time runs out, cut Part 3, never Part 2.

**What the seed supports.** Of the 16 submitters, 5 explain a decision they
made and the test that drove it, 9 only report that the work is done, and 2
forgot the repository link. So Claude can triage both on *submission state*
(missing, late, no link, already graded) and on *whether the student showed
their own judgement*. The five are kept clear of the missing, no-link and
pre-graded sets, so the contrast survives every filter.

## Part 1 – The pedagogical decision (about 4 minutes)

**1. Make the student's decisions visible**

> Olvasd el a 2. beadandó jelenlegi kiírását. Írd át úgy, hogy a működő program mellett a hallgató adjon le egy rövid specifikációt, egy általa talált hibát és az azt leleplező tesztet, valamint egy AI-javaslatot, amelyet elutasított vagy javított. Előbb mutasd meg a módosítást, ne mentsd el.

Expected: it reads the existing assignment and shows the proposed description in preview/dry-run form. Nothing is saved yet. The point to say aloud: the teacher chose what should count as evidence; nitro performed the Moodle work.

Say the limit while the preview is up: the criteria land in the assignment
text, not in a Moodle rubric. nitro has no rubric tool. An academic audience
rewards that sentence.

> Rendben, mentsd el.

Expected: it updates the same assignment with `save_assignment` and confirms the saved activity.

## Part 2 – The same decision, forty-four times (about 6 minutes)

This is the part the talk is buying. Say the number out loud before the first
prompt: *"Ez volt egy döntés. Most jön a negyvennégy."*

**2. Where does the class actually stand?**

> Hol tart a Demo kurzusom a 2. beadandóval? Bontsd szét: ki adott be időben, ki késve, ki nem adott be semmit, és kinél hiányzik a repó link.

Expected: it reads every submission and groups them. The four groups come from the seed, so this is solid. Read the group sizes aloud — that is the moment the class scale becomes visible.

**3. Which ones show the student's own judgement?**

> Olvasd el a beadásokat. Melyikből derül ki, hogy a hallgató maga hozott döntést, és melyikből csak az, hogy kész van?

Expected: it reads all of them and separates roughly five that name a decision and a test from the rest that only report completion. This is the talk's central claim landing on real course data: the finished program is the same, the evidence behind it is not.

Say it while the two lists are on screen: *"Ez az, amit a végtermékből nem látok."*

**4. Forty-four different replies (the core of the demo)**

> Írj mindegyik csoportnak más üzenetet, és a repó link nélkülieknél hivatkozz arra, amit ők maguk írtak a beadásba. Mutasd meg mindet, mielőtt bármit küldenél.

Expected: a preview listing the recipients and the exact text per group, with the two NOLINK students getting a reply that quotes their own excuse. No `{Keresztnév}`-style placeholder. Nothing is sent yet.

This is the slide-7 payoff. Say it plainly: *"Ezt kézzel nem írtam volna meg negyvennégyszer. Ezért maradt el eddig."*

> Mehet.

Expected: sent, with the number delivered. On the sandbox the fictitious students cannot receive mail, so Claude may say the message sits in Moodle only. That is fine, and it is worth saying on stage.

**5. And the teacher still decides** (optional, 1 minute)

> Nézd meg a beadott munkákat: van-e mindegyikben git repó link? Akiknél van, azokat fogadd el. A késve beadottakat a követelmények szerint pontozd.

Expected: it reads the submissions with their content and reads the requirements page (late work: at most half points). The preview has names: 10 points for on time, 5 for late. Two on-time submissions have no repository link ("a repót még feltöltöm", "e-mailben küldöm"); it flags them instead of grading them. Two on-time submissions are already graded 10 in the seed; it should leave them alone or say so.

> Rendben, mentsd el.

**6. Announcement** (optional)

> Tegyél ki egy közleményt: a 2. beadandó értékelése elkészült, és a 4. heti rekurziós anyaghoz hamarosan gyakorló kvíz jön. Előbb mutasd meg.

Expected: a preview that says how many will be emailed and why fewer than the whole class (fictitious accounts).

> Mehet.

## Part 3 – Quiz from the course's own material (optional, about 2 minutes)

**7. Practice quiz**

> A 4. heti anyagból írj 8 feleletválasztós gyakorlókérdést, és csinálj belőlük egy gyakorló kvízt péntek éjfélig, amit bármennyiszer kitölthetnek. Legyen rögtön látható a hallgatóknak.

Expected: it reads the week-4 page itself (`read_activity`), imports the questions, creates the quiz hidden with the practice review settings, then adds the questions and makes the quiz visible in the same step, so students never see it empty. The maximum grade equals the question total (8). It gives the quiz link.

Keep "legyen rögtön látható" (visible straight away) in the prompt: nitro cannot change a quiz after it has been created (pilot scope), so the quiz has to be opened while the questions are added.

**8. Show it**

> Add meg a kvíz linkjét.

Open the link on the projector.

## Closing (optional, 30 seconds)

> Van valami, amit ma nem tudtál megcsinálni? Ha igen, jelezd a nitro csapatnak.

Expected: it offers `send_feedback`, shows the exact message and sends it only after you approve. This shows that the tool also asks the teacher before sending.

## Attendee path (task 11.6)

What an attendee types after signing up and connecting Claude:

> Milyen kurzusaim vannak, és hol tart bennük a 2. beadandó?

## If something goes wrong on stage

- **Claude asks you to reconnect.** Reconnect, then repeat the last prompt. Tokens survive deploys now, so this should not happen during the demo; do not deploy on the day.
- **Wrong course.** Name it: "a Demo NN kurzusban". `list_courses` also says which site it is on.
- **A tool refuses.** Read the error aloud. It says what to do, and that is part of the story: nitro only does what the teacher is allowed to do.
- **Everything fails.** Play the fallback video (task 11.5).

## Seed notes

**Varied submission texts — done.** `provisioner.php` now seeds `REASONED`
(five students who name a decision and the test behind it) and `DONE_TEXTS`
(everyone else). Every entry keeps the repository link, so "who forgot the
link" still has exactly the two NOLINK answers, and `REASONED` is disjoint
from `MISSING`, `NOLINK` and `GRADED`. This changes seeded course content, so
rehearse against a course created after it.

**Forty-four students — not done, and probably not worth it.** `NAMES` holds
20 entries and the count is capped by `min(count(self::NAMES), …)`, so the
`students` setting cannot go above 20 until the array grows. Scaling also
needs `MISSING`, `LATE`, `NEVER_ACCESSED`, `GRADED`, `NOLINK` and `REASONED`
re-spread across the larger range — all six are hand-picked for 20 today.

The cheaper alternative is one sentence on stage: *"A kurzusomon 44 volt. A
próbakurzusban 20 — a lényeg ugyanaz."* Twenty individual replies is already
visibly more than anyone writes by hand, and the seed stays as tested.
