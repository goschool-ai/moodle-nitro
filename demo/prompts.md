# Demo prompts – BME Oktatói Klub, 2026-10-06

Every run starts from a fresh demo course: sign up a new account on the sandbox, wait for "Demo NN – Haladó programozás", then connect Claude to `https://moodle.tilosazai.org/local/nitro/mcp.php`. Messages, grades and announcements change the course, so a course is good for one run only.

The prompts name no tools. Claude has to find the way from the server instructions alone (task 11.1). If it needs an extra hint, note which prompt and what it got wrong.

## Part 1 – The weekly loop (about 6 minutes)

**1. Where does the course stand?**

> Hol tart a Demo kurzusom a 2. beadandóval? Kik vannak lemaradva?

Expected: it finds the course and lists who submitted on time, who submitted late and who has submitted nothing yet (`overdue`). It notices that the final deadline is still ahead.

**2. Reminder**

> Írj egy rövid, barátságos emlékeztetőt azoknak, akik még nem adták be. Írd bele a végső határidőt. Mutasd meg, mielőtt elküldöd.

Expected: a preview with the recipients' names and the exact text, no `{Keresztnév}`-style placeholder, and the final deadline taken from Moodle.

> Mehet.

Expected: sent, with the number delivered. On the sandbox the fictitious students cannot receive mail, so Claude may say the message sits in Moodle only. That is fine, and it is worth saying on stage.

**3. Accept the submissions**

> Nézd meg a beadott munkákat: van-e mindegyikben git repó link? Akiknél van, azokat fogadd el. A késve beadottakat a követelmények szerint pontozd.

Expected: it reads the submissions with their content and reads the requirements page (late work: at most half points). The preview has names: 10 points for on time, 5 for late, and it flags submissions without a link instead of grading them.

> Rendben, mentsd el.

**4. Announcement**

> Tegyél ki egy közleményt: a 2. beadandó értékelése elkészült, és jövő héten a rekurzióval folytatjuk. Előbb mutasd meg.

Expected: a preview that says how many will be emailed and why fewer than the whole class (fictitious accounts).

> Mehet.

## Part 2 – Quiz from the course's own material (about 4 minutes)

**5. Practice quiz**

> A 4. heti anyagból írj 8 feleletválasztós gyakorlókérdést, és csinálj belőlük egy gyakorló kvízt péntek éjfélig, amit bármennyiszer kitölthetnek. Legyen rögtön látható a hallgatóknak.

Expected: it reads the week-4 page itself (`read_activity`), imports the questions, creates the quiz visible with the practice review settings, and adds the questions. The maximum grade equals the question total (8). It gives the quiz link.

Say "legyen rögtön látható" (visible straight away) in the prompt: nitro cannot change a quiz after it has been created (pilot scope), so visibility has to be right the first time.

**6. Show it**

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
