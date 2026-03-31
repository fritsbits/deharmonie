# Design Spec — Inschrijvingsflow

_Date: 2026-03-30_

## Overview

Bring the registration flow in line with the UX strategy: registration is a confirmed reservation, not a request awaiting approval. Staff handles everything from email. The admin is a light read-only reference.

UX planning docs: `docs/ux/inschrijven/`

---

## Section 1 — Data layer

### Migration
Drop the `status` column from `deelnameverzoeken`. It was used for the admin status toggle, which is being removed.

### Model: `Deelnameverzoek`
- Remove `status` from `$fillable`
- Remove any `status` cast

### `isBeschikbaar()` on `Activiteit`
Currently counts `te_contacteren` and `afgehandeld` deelnameverzoeken separately. With `status` gone, simplify to a plain count of all deelnameverzoeken for that activity against `max_deelnemers`.

### `RegistrationForm::submit()`
- Remove `'status' => 'te_contacteren'` from the `Deelnameverzoek::create()` call
- Remove the `isBeschikbaar()` check — the Blade view gates this before the component mounts

---

## Section 2 — Activity page, overview + registration form

### `show.blade.php` — fully booked state
Within the `@if ($activiteit->status->value === 'gepubliceerd')` block, add a second condition:

- If `!$activiteit->isBeschikbaar()`: replace the entire section (divider, CTA text, form card) with a bilingual notice:
  > "Deze activiteit is volgeboekt. / Cette activité est complète."
- If available: show the form as now

The mobile sticky CTA is already gated on `gepubliceerd` — no change needed there.

### "Ingeschreven" badge — detail page (`show.blade.php`)
Add an Alpine.js block near the activity title that reads `localStorage` for a `bookedActivities` key (JSON array of activity IDs). If the current activity's ID is present, show a small "Ingeschreven" / "Inscrit(e)" badge. The badge is informational only — does not block the form.

### "Ingeschreven" badge — overview (`activity-filter.blade.php`)
Same Alpine.js check per activity card. Badge appears near the activity title.

### `registration-form.blade.php` — success state
Replace the current single-line success message with:
- Green checkmark
- "Je bent ingeschreven!" / "Vous êtes inscrit(e) !"
- Activity name, date + time, location
- "Je ontvangt een bevestiging per e-mail." / FR equivalent

On successful submit, `$submitted` flips to `true` and Livewire re-renders the component, mounting the success state div. That div carries an `x-init` that writes the activity ID into a `bookedActivities` JSON array in `localStorage`. No custom events needed — the mount itself is the trigger.

---

## Section 3 — Emails

### Visitor confirmation: `RegistratieBevestiging`
No changes to the mailable class or envelope.

Template (`registratie-bevestiging.blade.php`) changes:
- **Tone**: "Je bent ingeschreven" / "Vous êtes inscrit(e)" — confirmed, not pending
- **Body**:
  - Activity name, date, time, location
  - "Je hoeft niets meer te doen — je plaats is gereserveerd." / FR equivalent
  - De Harmonie phone (02 203 28 48) + email (info@deharmonie.be) for questions
  - Warm close: "Tot dan! / À bientôt!"

### Staff notification: `RegistratieNotificatie`
Mailable class changes:
- Add `reply_to: [new Address($this->verzoek->email, $this->verzoek->naam)]` to `envelope()`
- Update subject: `'Nieuwe inschrijving: ' . $this->activiteit->titel_nl . ' — ' . $this->activiteit->datum->locale('nl')->isoFormat('dddd D MMMM')`

Template (`registratie-notificatie.blade.php`): no structural changes needed.

---

## Section 4 — Admin (`DeelnameverzoekResource`)

### Table
- Remove `status` column
- Remove `toggle_status` action
- Remove `status` filter

### View action (slide-over)
Add a `Tables\Actions\ViewAction` to the actions array. Configure an infolist with:
- `TextEntry` for: naam, email, telefoon, bericht
- `TextEntry` for: activiteit naam, datum (formatted), tijdstip, aangevraagd (datetime)

### Infolist method
Add `public static function infolist(Infolist $infolist): Infolist` to the resource with the above entries. Requires imports: `Filament\Infolists\Infolist`, `Filament\Infolists\Components\TextEntry`.

---

## Testing

- Update `RegistrationFormTest`: remove any assertions on `status` field; assert `Deelnameverzoek` is created without status
- Update `WeekMenuTest` if it references `isBeschikbaar()` indirectly
- Add test: activity detail page shows fully booked notice when `max_deelnemers` is reached
- Add test: confirmation email has correct tone ("je bent ingeschreven"), includes activity details and contact info
- Add test: staff notification email has reply-to set to registrant's email, subject includes date
