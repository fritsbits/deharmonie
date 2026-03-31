# UX Scope — Inschrijvingsflow

## Registration form

Fields:
- Naam (required)
- E-mail (required)
- Telefoon (optional)
- Bericht (optional) — keep, even if rarely used

## Visitor confirmation email

- Tone: "You're registered, your spot is confirmed" — not "we'll get back to you"
- Content: activity name, date, time, location + De Harmonie phone/email for questions
- Warm close ("Tot dan!" / "À bientôt!")
- Bilingual (NL/FR based on visitor's language)

## Staff notification email

- Sent to: animatie@deharmonie.be
- Reply-to: registrant's email address (so staff can reply directly from inbox)
- Content: registrant name, email, phone, message + activity name and date
- Subject includes date: "Nieuwe inschrijving: [activiteit] — [dag datum]"
- Includes link to admin as light reference

## Admin (Filament)

- Read-only list of registrations
- No status toggle — staff handles everything via email
- Clicking a row opens a side panel with full details
- No create, no delete

## Fully booked state

- When `isBeschikbaar()` returns false: hide the entire registration section (CTA text + form card)
- Replace with a simple bilingual notice: "Deze activiteit is volgeboekt. / Cette activité est complète."
- No phone number, no further call to action

## Out of scope

- Reminders before the activity
- Cancellation flow (visitor or staff)
- Capacity management UI
- Assignment of registrations to staff members
- Status tracking beyond what's visible in the list
