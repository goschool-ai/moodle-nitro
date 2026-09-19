---
name: nitro
description: The public project page for the nitro Moodle plugin, a surface inside the GoSchool brand world.
colors:
  scholar-purple: "#5926eb"
  scholar-purple-deep: "#4a1fc4"
  ink-anthracite: "#383541"
  ink-soft: "#5d5a66"
  ink-mute: "#6c6976"
  warm-paper-cream: "#f0f0e6"
  soft-study-surface: "#fafaf5"
  studio-white: "#ffffff"
  spark-yellow: "#ebd926"
  sticker-yellow: "#f8d93c"
  signal-mint: "#26eaa6"
  sticker-mint: "#8af7c8"
  insight-magenta: "#c226eb"
  assistant-lavender: "#ece6fb"
  assistant-lavender-line: "#dcd2f7"
  assistant-lavender-ink: "#3a2596"
  preview-lavender: "#f4f0fe"
  chat-paper: "#f8f8f6"
  chat-border: "#e2e1dc"
  teacher-bubble: "#ebebee"
  chat-tag-grey: "#726f7b"
  hairline: "rgba(56, 53, 65, 0.14)"
  dark-band-lead: "#cfcdd6"
  dark-band-body: "#d9d7df"
typography:
  date-display:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "calc(190 * var(--k))"
    fontWeight: 800
    lineHeight: 1
    letterSpacing: "-0.055em"
  display:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "calc(66 * var(--k))"
    fontWeight: 800
    lineHeight: 1.07
    letterSpacing: "-0.035em"
  headline:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "clamp(2rem, 3.2vw, 2.9rem)"
    fontWeight: 700
    lineHeight: 1.12
    letterSpacing: "-0.03em"
  headline-statement:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "clamp(2.4rem, 5.4vw, 4.6rem)"
    fontWeight: 700
    lineHeight: 1.02
    letterSpacing: "-0.04em"
  title:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "1.35rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.02em"
  lead:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 300
    lineHeight: 1.6
  body:
    fontFamily: "Sora, system-ui, -apple-system, Segoe UI, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
  chat:
    fontFamily: "Sora, system-ui, sans-serif"
    fontSize: "calc(16 * var(--k))"
    fontWeight: 400
    lineHeight: 1.35
    letterSpacing: "-0.02em"
  sticker:
    fontFamily: "Pogonia, Sora, system-ui, sans-serif"
    fontWeight: 700
    lineHeight: 1.1
  mono:
    fontFamily: "ui-monospace, SF Mono, Menlo, Consolas, monospace"
    fontSize: "0.82rem"
    lineHeight: 1.45
rounded:
  xs: "4px"
  tag: "6px"
  sm: "8px"
  chat-bubble: "calc(7 * var(--k))"
  chat-card: "calc(10 * var(--k))"
  chat-window: "calc(12 * var(--k))"
  round: "50%"
spacing:
  rule-gap: "1.25rem"
  block: "2.5rem"
  column: "3rem"
  head-gap: "4rem"
  section: "clamp(4rem, 8vw, 7rem)"
  gutter-narrow: "1.5rem"
  gutter-wide: "3rem"
  container: "75rem"
components:
  button-primary:
    backgroundColor: "{colors.scholar-purple}"
    textColor: "{colors.studio-white}"
    rounded: "{rounded.sm}"
    width: "calc(358 * var(--k))"
    height: "calc(74 * var(--k))"
  lang-toggle:
    backgroundColor: "transparent"
    textColor: "{colors.ink-anthracite}"
    rounded: "{rounded.sm}"
    width: "calc(51 * var(--k))"
    height: "calc(43 * var(--k))"
  copy-field:
    backgroundColor: "{colors.studio-white}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.mono}"
    rounded: "{rounded.sm}"
    padding: "0.7rem 0.8rem"
  copy-button:
    backgroundColor: "{colors.ink-anthracite}"
    textColor: "{colors.warm-paper-cream}"
    padding: "0 1rem"
  copy-button-hover:
    backgroundColor: "{colors.scholar-purple}"
  chat-window:
    backgroundColor: "{colors.chat-paper}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.chat}"
    rounded: "{rounded.chat-window}"
  chat-bubble:
    backgroundColor: "{colors.teacher-bubble}"
    textColor: "{colors.ink-anthracite}"
    rounded: "{rounded.chat-bubble}"
    padding: "calc(7 * var(--k)) calc(13 * var(--k)) calc(8 * var(--k))"
  chat-reply:
    backgroundColor: "{colors.assistant-lavender}"
    textColor: "{colors.assistant-lavender-ink}"
    rounded: "{rounded.chat-bubble}"
    padding: "calc(8 * var(--k)) calc(18 * var(--k)) calc(9 * var(--k)) calc(12 * var(--k))"
  chat-preview:
    backgroundColor: "{colors.preview-lavender}"
    textColor: "{colors.ink-anthracite}"
    rounded: "{rounded.chat-card}"
    padding: "calc(12 * var(--k)) calc(14 * var(--k)) calc(12 * var(--k)) calc(18 * var(--k))"
  chat-go:
    backgroundColor: "{colors.scholar-purple}"
    textColor: "{colors.studio-white}"
    rounded: "{rounded.sm}"
    padding: "calc(13 * var(--k)) calc(18 * var(--k))"
  chat-tag:
    backgroundColor: "{colors.chat-tag-grey}"
    textColor: "{colors.studio-white}"
    rounded: "{rounded.tag}"
    padding: "calc(4 * var(--k)) calc(11 * var(--k))"
  sticker-yellow:
    backgroundColor: "{colors.sticker-yellow}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.sticker}"
    rounded: "{rounded.xs}"
  sticker-mint:
    backgroundColor: "{colors.sticker-mint}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.sticker}"
    rounded: "{rounded.xs}"
  pill-now:
    backgroundColor: "{colors.sticker-mint}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.sticker}"
    rounded: "{rounded.xs}"
    padding: "0.15rem 0.55rem 0.2rem"
  pill-pilot:
    backgroundColor: "{colors.sticker-yellow}"
    textColor: "{colors.ink-anthracite}"
    typography: "{typography.sticker}"
    rounded: "{rounded.xs}"
    padding: "0.15rem 0.55rem 0.2rem"
  pill-plan:
    backgroundColor: "{colors.assistant-lavender}"
    textColor: "{colors.assistant-lavender-ink}"
    typography: "{typography.sticker}"
    rounded: "{rounded.xs}"
    padding: "0.15rem 0.55rem 0.2rem"
  step-marker:
    backgroundColor: "{colors.scholar-purple}"
    textColor: "{colors.warm-paper-cream}"
    rounded: "{rounded.round}"
    size: "4.2rem"
  admin-band:
    backgroundColor: "{colors.ink-anthracite}"
    textColor: "{colors.warm-paper-cream}"
    padding: "clamp(4rem, 8vw, 7rem) 0"
---

# Design System: nitro

## Overview

**Creative North Star: "The Week in One Conversation"**

This page is a surface inside the GoSchool brand world. The parent system lives at `/Users/rp/Dev/goschool/goschool-web/DESIGN.md` and governs everything this file does not narrow: the palette, Sora and Pogonia, the Flat-by-Default and One Clear Spark rules, the Accent Rule and the sticker lift. This file records what the nitro site built on top of it: a measured first viewport scaled by one unit, a chat illustration with its own component set, and the sections below the fold that reuse the parent's rules, pills and dark band.

The first viewport is composed, not flowed. Every element in the hero is placed at a measured coordinate on a 1536 x 1024 frame and scaled by `--k`, so the date, the headline, the purple button and the chat keep the proportions of the approved comp at every desktop width. Below 60rem the hero stops being measured and reflows into one column. Everything below the fold is ordinary document flow on cream, in a 75rem container.

Scope: this DESIGN.md governs the GitHub Pages site in `docs/` (Hungarian at `docs/index.html`, English at `docs/en/index.html`). The plugin's own Moodle pages (the OAuth consent screen, settings and profile pages) are Moodle-native, render through the Moodle theme and its components, and are NOT governed by this file.

**Key Characteristics:**
- Cream ground, anthracite ink, one electric purple for action (inherited).
- A measured hero: absolute placement in `--k` units against a 1536px reference frame.
- The assistant's voice is lavender; the teacher's is neutral grey.
- Pogonia appears only on tilted stickers and status pills, each with the parent's sticker lift.
- Accent rules open blocks by meaning: purple principle, mint outcome, magenta limit; mint only on dark.
- Hungarian and English pages share one stylesheet and identical markup structure.

## Colors

The inherited GoSchool palette, plus a lavender family that belongs to the assistant and a set of quiet greys that build the chat illustration.

### Primary
- **Scholar Purple** (`scholar-purple`): the one action colour. The hero button, the "Mehet" button inside the preview, the step markers, link colour, focus rings, selection, and the default leading rule. `scholar-purple-deep` is declared for pressed states but is not yet used by a shipped rule.

### Secondary
- **Assistant Lavender** (`assistant-lavender`, with `assistant-lavender-ink` text and `assistant-lavender-line` border): the assistant's voice. Every nitro reply bubble and the "tervezett / planned" pill. The preview card sits on the lighter `preview-lavender` inside a lavender-line border.
- **Sticker Yellow** and **Sticker Mint** (`sticker-yellow`, `sticker-mint`): the paper colours of stickers and status pills. They are lighter than the parent's signal yellow and mint so Pogonia ink stays legible on them.

### Tertiary
- **Signal Mint** (`signal-mint`): the outcome leading rule on light, and every leading rule on the dark band.
- **Insight Magenta** (`insight-magenta`): the limit leading rule. Nowhere else.
- **Spark Yellow** (`spark-yellow`): focus ring on dark surfaces (the copy button, admin-band links). Not a fill.

### Neutral
- **Ink Anthracite** (`ink-anthracite`): body text, and the fill of the dark admin band and the copy button.
- **Ink Soft** (`ink-soft`): leads, step and rule descriptions, footer.
- **Ink Mute** (`ink-mute`): chat timestamps and the sub-lines of the verb lists.
- **Warm Paper Cream** (`warm-paper-cream`): the page ground; also text on purple markers and on the dark band.
- **Studio White** (`studio-white`): the trust section's field and the copy field.
- **Chat Paper**, **Chat Border**, **Teacher Bubble**, **Chat Tag Grey**: the chat window's own surface, its 1px border, the teacher's bubble, and the "illusztráció" label.
- **Hairline** (`hairline`): 1px dividers above the clients block and between verb-list rows.
- **Dark Band Lead / Body** (`dark-band-lead`, `dark-band-body`): secondary text on anthracite.

### Named Rules
**The Two Voices Rule.** In the chat the teacher is grey (`teacher-bubble`) and the assistant is lavender (`assistant-lavender` with a purple spark glyph). Never swap them, and never give the teacher purple: purple is the action the teacher takes, not the teacher.

**The Rule Colour Means Something Rule.** Leading rules in a set take colour by meaning, as in the parent Accent Rule: purple for a principle, mint for an outcome, magenta for a limit. On the anthracite band every rule is mint.

## Typography

**Display Font:** Sora (with `system-ui, -apple-system, Segoe UI, sans-serif`)
**Body Font:** Sora
**Accent Font:** Pogonia 700 (with Sora), stickers and status pills only
**Mono:** `ui-monospace, SF Mono, Menlo, Consolas, monospace`, the connector URL and admin code only

**Character:** Sora set at 800 and tightly tracked for the date and headline, 700 for section heads, 300 for leads: the parent's Confident Contrast rule, pushed further at the top of the page.

### Hierarchy
- **Date display** (800, `190 * k`, 1, -0.055em): the opening date only ("okt. 7." / "Oct 7"). One per page. Reflows to `clamp(5.5rem, 27vw, 11rem)` below 60rem.
- **Display** (800, `66 * k`, 1.07, -0.035em): the hero headline, 720k wide, two lines. `clamp(2rem, 8.4vw, 3.8rem)` below 60rem.
- **Headline** (700, `clamp(2rem, 3.2vw, 2.9rem)`, 1.12, -0.03em, balanced): section heads.
- **Headline statement** (700, `clamp(2.4rem, 5.4vw, 4.6rem)`, 1.02, -0.04em, max 14ch): the trust section's head, the page's one statement-sized heading below the fold.
- **Title** (700, 1.35rem, -0.02em): step and clients heads. Verb heads step up to 1.75rem; rule terms sit at 1.5rem; dark-band heads at 1.3rem.
- **Lead** (300, 1.125rem, max 46ch, `ink-soft`): the paragraph beside each section head.
- **Body** (400, 1rem, 1.6): running text; descriptions capped at 34 to 48ch.
- **Chat** (400, `16 * k`, 1.35, -0.02em): bubbles and replies. Speaker names are 700 at `13 * k`; timestamps use tabular numerals.

### Named Rules
**The Pogonia Stays on Paper Rule.** Pogonia is set only on a sticker or a status pill, that is, on a tilted, lifted paper chip. Headings, the button, nav and chat text are Sora.

## Layout

Two layout regimes, switched at 60rem.

**The measured hero (above 60rem).** The hero is a `min(100vw, 1536px)` wide, `1024k` tall positioned frame. `--k` is `min(100vw, 1536px) / 1536`: one reference pixel of the 1536 x 1024 comp. Every hero coordinate, size, gap and font size is written as `calc(n * var(--k))`: nav at 240k tall, brand at (89, 94), date at (94, 318), headline at (99, 506), button at (100, 690) 358 x 74, chat at (855, 205) 579 x 712. The chat uses the same unit internally (a 95k timestamp column, 31k between exchanges).

**The reflow (60rem and below).** Every hero child becomes static, the frame becomes a flex column with `1.25rem 1.5rem 3.5rem` padding, the nav wraps with the links on their own row (first and last link hidden), the button goes full width to 26rem max at 3.5rem tall, and the chat re-pins `--k` to a fixed `0.92px` so its internals stay readable at phone widths.

**Below the fold.** Sections sit in a `min(100% - 3rem, 75rem)` container (`- 6rem` from 48rem) with `clamp(4rem, 8vw, 7rem)` vertical padding. Each section opens with a head that splits into two columns from 56rem (heading left, lead right, bottom-aligned); content grids go to three columns (steps, clients, verbs) or two (trust rules from 48rem, admin at 1.1fr / 1fr from 56rem) at the same breakpoint.

**The Measured Frame Rule.** Anything added to the hero above 60rem is placed in `--k` units against the 1536 frame, never in rem or percent. Nothing below the hero uses `--k`.

## Elevation & Depth

Flat by default, as in the parent. Depth appears in exactly three places.

### Shadow Vocabulary
- **Sticker lift** (`box-shadow: 0 4px 14px rgba(56, 53, 65, 0.16)`): every sticker and status pill; the parent's value, unchanged.
- **Chat window float** (`box-shadow: 0 10px 30px rgba(56, 53, 65, 0.08)`): the chat illustration only; it is an object laid on the page, not a card.
- **Go nudge** (animated ring `0 0 0 7px rgba(89, 38, 235, 0.18)` at 40%): the one-time pulse on "Mehet" after load. Not a resting shadow.

### Named Rules
**The Only the Chat Floats Rule.** The chat window is the page's single lifted surface. Steps, rules, verbs and clients are flat, separated by hairlines, accent rules and colour fields.

## Shapes

Restrained corners from the parent: 8px for the button, the language toggle and the copy field; 4px for stickers and pills; 6px for the chat's label. The chat scales its own radii with `--k` (12k window, 10k preview, 8k icon and go button, 7k bubbles). Step markers are the page's only circles (4.2rem). Stickers tilt counter-clockwise, and the angle falls with size: -14deg (timer), -11deg (hero date), -6deg (inline), -5deg (verb), -4deg (pills). The steps row is joined by one dashed 2px line (6px dash, 6px gap, ink at 35%) behind the markers, from 56rem.

## Components

Components are few and literal: a button that goes somewhere, a field you copy from, and a drawn chat that shows the product working.

### Buttons
- **Primary (hero):** purple, white 700 text, 8px radius, measured at 358 x 74k with `24 * k` type. Hover drops opacity to 0.88; active scales to 0.985; focus ring switches to ink. One per page. Its label and target change by date through `site.js` (before 7 October it points to the steps; from then it opens signup).
- **Copy button:** anthracite joined to the copy field's right edge by a 1.5px ink border, cream 600 text at 0.9rem; hover turns purple; focus is a 3px spark-yellow inset ring. On click the label swaps to "Másolva / Copied" for 1.8s.
- **Language toggle:** a 1.5px `#bdbbb6` outlined 8px square with the two-letter code; hover darkens the border to ink.

### Copy field
- **Style:** white, 1.5px ink border, 8px radius, clipped; mono 0.82rem URL that wraps only at `<wbr>` breaks.

### Chat illustration (signature)
- **Window:** chat-paper surface, 1px chat-border, 12k radius, the chat float shadow, and a grey "illusztráció / illustration" tag pinned top-left. The tag is required whenever the chat depicts product behaviour that is not a screenshot.
- **Exchange:** a timestamp column (95k, `ink-mute`, tabular) beside a stack of turns; each turn is a 700 speaker name above its bubble.
- **Bubble (teacher):** teacher-bubble grey, ink text, 7k radius.
- **Reply (assistant):** lavender, lavender-ink text, 7k radius, led by the 20k purple four-point spark glyph (inline SVG).
- **Preview row:** the approval moment. A preview-lavender card with a lavender-line border and 10k radius: a 42k purple icon tile (hidden below 60rem), a bold "Előnézet / Preview" label with a soft count, and the purple "Mehet / Send" button pushed right.
- **Timer sticker:** a mint sticker on the window's top-right corner at -14deg.
- **Motion:** with JS and no reduced-motion preference, exchanges arrive in order (0.7s, `cubic-bezier(0.16, 1, 0.3, 1)`, 10px rise with a 3px blur clearing, 0.7s apart, the reply 0.35s after its question), "Mehet" nudges once at 1.6s, and the timer stamps in at 3.2s. Reduced motion shows the finished chat.

### Stickers and status pills
- **Sticker:** Pogonia 700, ink on sticker-yellow or sticker-mint, 4px radius, sticker lift, tilted. Used absolutely in the hero (date, timer) and inline after a heading (`0.42em` beside a section head, `0.95rem` beside a verb head).
- **Status pill:** the same chip at 0.85rem, tilted -4deg, after a client name. Colour carries status: mint for available now, yellow for pilot, lavender for planned.

### Steps sequence
An ordered list of three, counted by CSS: 4.2rem purple circles with cream 800 numerals, a 1.35rem title, a description capped at 34ch, joined by the dashed line from 56rem.

### Clients list
A hairline-topped block under the steps: a title, then three columns of client name plus status pill plus a 0.95rem soft description.

### Trust rules
A definition list on the white trust field, two columns from 48rem. Each item opens on a 4px leading rule with 1.25rem above its 1.5rem term; colour by meaning (see Colors). The section head sets the statement headline with one purple-underlined word (0.12em underline, 0.14em offset).

### Verb lists
Three columns, each a 1.75rem verb head over a list of rows separated by hairlines, each row a claim with a 0.9rem `ink-mute` sub-line.

### Dark admin band
The admin section on anthracite with cream text. Heads open on 4px mint leading rules; list markers and links are sticker-mint; inline code sits on 10% cream; the milestones table uses tabular numerals, mint row heads and 16% cream dividers; focus rings are spark-yellow.

### Navigation
Brand lockup (GoSchool logo, a 1.5px divider, "nitro" in Sora 800) left; four 600-weight ink links that turn purple on hover; the language toggle right. All placed in `--k` above 60rem.

### Footer
Cream, 2.5rem padding, the GoSchool logo at 7rem and one line of `ink-soft` 0.9rem text, spaced apart and wrapping.

## Do's and Don'ts

### Do:
- **Do** treat goschool-web DESIGN.md as the parent; this file only narrows it for the nitro site.
- **Do** write every hero measurement as `calc(n * var(--k))` against the 1536 x 1024 frame, and keep the 60rem reflow working.
- **Do** keep the teacher grey and the assistant lavender in any chat illustration, and label it as an illustration.
- **Do** colour leading rules by meaning (purple principle, mint outcome, magenta limit) and use mint only on the dark band.
- **Do** give every sticker and pill the sticker lift and a counter-clockwise tilt.
- **Do** keep the Hungarian and English pages structurally identical, sharing `site.css`.

### Don't:
- **Don't** use `--k` outside the hero, or rem units inside it above 60rem.
- **Don't** lift anything but the chat window and the stickers; sections stay flat.
- **Don't** set Pogonia anywhere but a sticker or a status pill.
- **Don't** add a second purple button to a viewport; "Mehet" inside the illustration is the only other purple button, and it is part of the drawing.
- **Don't** apply this file to nitro's Moodle-native pages (consent, settings, profile); those follow the Moodle theme.
