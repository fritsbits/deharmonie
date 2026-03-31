# Inschrijvingsflow Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring the registration flow in line with the UX spec — registration is a confirmed reservation, staff works from email, admin is a light read-only reference.

**Architecture:** Drop the `status` column from `deelnameverzoeken` (no longer needed without the toggle). Gate the fully booked state in the Blade view rather than on form submit. Track "booked" activities client-side in `localStorage` via Alpine.js.

**Tech Stack:** Laravel 13, Livewire 3, Alpine.js, Filament 4, Tailwind v4, PHPUnit 12

---

## File Map

**Create:**
- `database/migrations/YYYY_MM_DD_drop_status_from_deelnameverzoeken_table.php`

**Modify:**
- `database/factories/DeelnameverzoekFactory.php` — remove `status` field
- `app/Models/Deelnameverzoek.php` — remove `status` from `$fillable`
- `app/Models/Activiteit.php` — simplify `isBeschikbaar()` (remove `whereIn('status', ...)`)
- `app/Livewire/RegistrationForm.php` — remove `status` from create, remove `isBeschikbaar()` check
- `resources/views/livewire/registration-form.blade.php` — new success state + `x-init` localStorage write
- `resources/views/activiteiten/show.blade.php` — fully booked branch + "ingeschreven" badge
- `resources/views/livewire/activity-filter.blade.php` — "ingeschreven" badge per card
- `resources/views/mail/registratie-bevestiging.blade.php` — confirmed tone + contact details
- `app/Mail/RegistratieNotificatie.php` — add `reply_to`, date in subject
- `app/Filament/Resources/DeelnameverzoekResource.php` — remove toggle, add infolist + `ViewAction`
- `lang/nl/activities.php` — add `booked`, `fully_booked`
- `lang/fr/activities.php` — add `booked`, `fully_booked`
- `tests/Feature/RegistrationFormTest.php` — update + add tests

---

## Task 1: Drop status column — migration, model, factory

**Files:**
- Create: `database/migrations/YYYY_MM_DD_drop_status_from_deelnameverzoeken_table.php`
- Modify: `app/Models/Deelnameverzoek.php`
- Modify: `database/factories/DeelnameverzoekFactory.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Update `test_successful_registration_creates_record` to not assert on `status`**

In `tests/Feature/RegistrationFormTest.php`, change the `assertDatabaseHas` call:

```php
$this->assertDatabaseHas('deelnameverzoeken', [
    'activiteit_id' => $activiteit->id,
    'naam' => 'Jan Janssen',
    'email' => 'jan@example.com',
]);
```

- [ ] **Step 2: Run the test to confirm it currently passes (before migration)**

```bash
php artisan test --compact --filter=test_successful_registration_creates_record
```

Expected: PASS (the assertion no longer checks `status`, so it passes against the current schema)

- [ ] **Step 3: Create the migration**

```bash
php artisan make:migration drop_status_from_deelnameverzoeken_table --no-interaction
```

Open the generated file and replace its contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deelnameverzoeken', function (Blueprint $table): void {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('deelnameverzoeken', function (Blueprint $table): void {
            $table->enum('status', ['te_contacteren', 'afgehandeld'])->default('te_contacteren')->after('bericht');
        });
    }
};
```

- [ ] **Step 4: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: `Migrating: ...drop_status_from_deelnameverzoeken_table` → `Migrated`

- [ ] **Step 5: Update `Deelnameverzoek` model — remove `status` from `$fillable`**

Replace `$fillable` in `app/Models/Deelnameverzoek.php`:

```php
protected $fillable = [
    'activiteit_id', 'naam', 'email', 'telefoon', 'bericht',
];
```

- [ ] **Step 6: Update `DeelnameverzoekFactory` — remove `status` field**

Replace the `definition()` method in `database/factories/DeelnameverzoekFactory.php`:

```php
public function definition(): array
{
    return [
        'activiteit_id' => Activiteit::factory(),
        'naam' => $this->faker->name(),
        'email' => $this->faker->safeEmail(),
        'telefoon' => $this->faker->phoneNumber(),
        'bericht' => $this->faker->sentence(),
    ];
}
```

- [ ] **Step 7: Run format + registration tests**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all tests pass except `test_form_still_shows_when_at_capacity` (it will fail — that test will be replaced in Task 2)

- [ ] **Step 8: Commit**

```bash
git add database/migrations/ app/Models/Deelnameverzoek.php database/factories/DeelnameverzoekFactory.php tests/Feature/RegistrationFormTest.php
git commit -m "feat: drop status column from deelnameverzoeken"
```

---

## Task 2: Simplify isBeschikbaar() and RegistrationForm

**Files:**
- Modify: `app/Models/Activiteit.php`
- Modify: `app/Livewire/RegistrationForm.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Replace `test_form_still_shows_when_at_capacity` with a new test**

In `tests/Feature/RegistrationFormTest.php`, delete the old test and add:

```php
public function test_fully_booked_activity_shows_notice_on_detail_page(): void
{
    $activiteit = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'max_deelnemers' => 1,
    ]);
    Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

    $response = $this->get(route('nl.activiteiten.show', $activiteit->slug));

    $response->assertStatus(200);
    $response->assertSee('volgeboekt');
    $response->assertDontSee(__('forms.submit'));
}
```

- [ ] **Step 2: Run the new test to confirm it fails**

```bash
php artisan test --compact --filter=test_fully_booked_activity_shows_notice_on_detail_page
```

Expected: FAIL — the notice is not yet rendered

- [ ] **Step 3: Simplify `isBeschikbaar()` in `app/Models/Activiteit.php`**

Replace the method:

```php
public function isBeschikbaar(): bool
{
    if ($this->max_deelnemers === null) {
        return true;
    }

    return $this->deelnameverzoeken()->count() < $this->max_deelnemers;
}
```

- [ ] **Step 4: Update `RegistrationForm::submit()` — remove status + remove isBeschikbaar check**

Replace the `submit()` method in `app/Livewire/RegistrationForm.php`:

```php
public function submit(): void
{
    if ($this->honeypot !== '') {
        return;
    }

    $key = 'registration:' . request()->ip();
    if (RateLimiter::tooManyAttempts($key, 5)) {
        $this->addError('email', __('forms.rate_limit'));
        return;
    }
    RateLimiter::hit($key, 60);

    $this->validate();

    $verzoek = Deelnameverzoek::create([
        'activiteit_id' => $this->activiteit->id,
        'naam' => $this->naam,
        'email' => $this->email,
        'telefoon' => $this->telefoon ?: null,
        'bericht' => $this->bericht ?: null,
    ]);

    $locale = app()->getLocale();

    Mail::send(new RegistratieNotificatie($verzoek, $this->activiteit));
    Mail::send(new RegistratieBevestiging($verzoek, $this->activiteit, $locale));

    $this->submitted = true;
}
```

- [ ] **Step 5: Run format + all registration tests**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all tests still fail for `test_fully_booked_activity_shows_notice_on_detail_page` (view not updated yet). All others pass.

- [ ] **Step 6: Commit**

```bash
git add app/Models/Activiteit.php app/Livewire/RegistrationForm.php tests/Feature/RegistrationFormTest.php
git commit -m "refactor: simplify isBeschikbaar and remove status from RegistrationForm"
```

---

## Task 3: Add translation keys

**Files:**
- Modify: `lang/nl/activities.php`
- Modify: `lang/fr/activities.php`

- [ ] **Step 1: Add keys to `lang/nl/activities.php`**

Add to the array (anywhere in the file):

```php
'booked' => 'Ingeschreven',
'fully_booked' => 'Deze activiteit is volgeboekt.',
```

- [ ] **Step 2: Add keys to `lang/fr/activities.php`**

Add to the array:

```php
'booked' => 'Inscrit(e)',
'fully_booked' => 'Cette activité est complète.',
```

- [ ] **Step 3: Commit**

```bash
git add lang/nl/activities.php lang/fr/activities.php
git commit -m "feat: add booked and fully_booked translation keys"
```

---

## Task 4: Fully booked notice + "ingeschreven" badge on detail page

**Files:**
- Modify: `resources/views/activiteiten/show.blade.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Update the registration section in `show.blade.php`**

Find the block starting with `{{-- Registration form --}}` (around line 126). Replace the entire `@if / @elseif / @endif` block with:

```blade
{{-- Registration form --}}
@if ($activiteit->status->value === 'gepubliceerd')
    <hr style="border: none; border-top: 1px solid var(--color-brand-border-green); margin: 2rem 0;">

    @if (!$activiteit->isBeschikbaar())
        {{-- Fully booked notice --}}
        <div class="rounded-lg p-5"
             style="border: 1px solid var(--color-brand-gray); background: white; max-width: 640px; color: var(--color-brand-muted); font-size: 1.1rem;">
            {{ __('activities.fully_booked') }}
        </div>
    @else
        {{-- Register CTA --}}
        <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1.5rem; max-width: 60ch;">
            <div style="width: 52px; height: 52px; background: var(--color-brand-green); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <p style="font-size: 1.1rem; color: var(--color-brand-dark); line-height: 1.65; margin: 0;">
                {{ __('activities.register_cta_heading') }}
                <a href="tel:0220328048" class="font-semibold hover:underline" style="color: var(--color-brand-blue); white-space: nowrap;">02&nbsp;203&nbsp;28&nbsp;48</a>,
                <a href="mailto:info@deharmonie.be" class="hover:underline" style="color: var(--color-brand-blue);">info@deharmonie.be</a>
                {{ __('activities.register_cta_form_sub') }}
            </p>
        </div>
        <div class="rounded-lg p-6" id="inschrijven" x-ref="form"
             style="border: 1px solid var(--color-brand-gray); background: white; max-width: 640px;">
            <livewire:registration-form :activiteit="$activiteit" />
        </div>
    @endif
@elseif ($activiteit->status->value === 'geannuleerd')
    <p class="text-sm italic" style="color: var(--color-brand-muted)">
        {{ __('activities.registration_closed') }}
    </p>
@endif
```

- [ ] **Step 2: Add "ingeschreven" badge near the title in `show.blade.php`**

Find the `<h1>` tag for the activity title. Just after the closing `</h1>`, add the badge span (before the `@if ($showAlt)` block):

```blade
{{-- "Ingeschreven" badge — client-side via localStorage --}}
<span
    x-data="{ booked: false }"
    x-init="
        const ids = JSON.parse(localStorage.getItem('bookedActivities') || '[]');
        booked = ids.includes({{ $activiteit->id }});
    "
    x-show="booked"
    style="display: inline-block; background: var(--color-brand-green); color: white; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; font-family: var(--font-sans); letter-spacing: 0.04em; text-transform: uppercase; vertical-align: middle; margin-left: 0.5rem;">
    {{ __('activities.booked') }}
</span>
```

- [ ] **Step 3: Run the fully booked test**

```bash
php artisan test --compact --filter=test_fully_booked_activity_shows_notice_on_detail_page
```

Expected: PASS

- [ ] **Step 4: Run all registration tests**

```bash
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all pass

- [ ] **Step 5: Hide mobile sticky CTA when fully booked**

Find the mobile sticky CTA block near the bottom of `show.blade.php` (the `md:hidden` fixed bar). Change its condition from:

```blade
@if ($activiteit->status->value === 'gepubliceerd')
```

to:

```blade
@if ($activiteit->status->value === 'gepubliceerd' && $activiteit->isBeschikbaar())
```

This prevents the "Inschrijven →" button from appearing when there's no form to scroll to.

- [ ] **Step 6: Run all registration tests**

```bash
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all pass

- [ ] **Step 7: Commit**

```bash
git add resources/views/activiteiten/show.blade.php tests/Feature/RegistrationFormTest.php
git commit -m "feat: fully booked notice and ingeschreven badge on detail page"
```

---

## Task 5: "Ingeschreven" badge on activity overview

**Files:**
- Modify: `resources/views/livewire/activity-filter.blade.php`

- [ ] **Step 1: Add badge to each activity card in `activity-filter.blade.php`**

Find the `<div style="display: flex; align-items: center; gap: 0.5rem;">` that wraps the title `<p>` and the geannuleerd badge. Add the "ingeschreven" badge after the geannuleerd badge `@endif`:

```blade
<div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
    <p style="font-weight: 700; font-size: 1.625rem; line-height: 1.2; color: var(--color-brand-blue); font-family: var(--font-sans); margin: 0;">
        {{ $activiteit->titel }}
    </p>
    @if ($activiteit->status->value === 'geannuleerd')
        <x-badge type="geannuleerd">&times;</x-badge>
    @endif
    <span
        x-data="{ booked: false }"
        x-init="
            const ids = JSON.parse(localStorage.getItem('bookedActivities') || '[]');
            booked = ids.includes({{ $activiteit->id }});
        "
        x-show="booked"
        style="display: inline-block; background: var(--color-brand-green); color: white; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; font-family: var(--font-sans); letter-spacing: 0.04em; text-transform: uppercase;">
        {{ __('activities.booked') }}
    </span>
</div>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/livewire/activity-filter.blade.php
git commit -m "feat: ingeschreven badge on activity overview"
```

---

## Task 6: Update registration form success state + localStorage write

**Files:**
- Modify: `resources/views/livewire/registration-form.blade.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Replace the success state in `registration-form.blade.php`**

Replace the entire `@if ($submitted)` block:

```blade
@if ($submitted)
    <div
        x-data
        x-init="
            const ids = JSON.parse(localStorage.getItem('bookedActivities') || '[]');
            if (!ids.includes({{ $activiteit->id }})) {
                ids.push({{ $activiteit->id }});
                localStorage.setItem('bookedActivities', JSON.stringify(ids));
            }
        "
        class="rounded-lg p-6 text-center"
        style="background-color: rgba(129,181,156,0.12); border: 1px solid var(--color-brand-green);">
        <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="var(--color-brand-green)" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="font-bold text-lg mb-3" style="color: var(--color-brand-dark);">
            @if (app()->getLocale() === 'fr')
                Vous êtes inscrit(e)&nbsp;!
            @else
                Je bent ingeschreven!
            @endif
        </p>
        <p style="font-size: 1.05rem; color: var(--color-brand-dark); font-weight: 600; margin-bottom: 0.25rem;">
            {{ $activiteit->titel }}
        </p>
        <p style="font-size: 0.95rem; color: var(--color-brand-muted); margin-bottom: 0.25rem;">
            {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY')) }}
            &middot; {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur)&ndash;{{ substr($activiteit->einduur, 0, 5) }}@endif
        </p>
        <p style="font-size: 0.95rem; color: var(--color-brand-muted); margin-bottom: 1rem;">
            {{ $activiteit->locatie }}
        </p>
        <p style="font-size: 0.9rem; color: var(--color-brand-muted);">
            @if (app()->getLocale() === 'fr')
                Vous recevrez une confirmation par e-mail.
            @else
                Je ontvangt een bevestiging per e-mail.
            @endif
        </p>
    </div>
@else
```

Make sure to close the `@else` block with the existing form HTML, ending with `@endif`.

- [ ] **Step 2: Add a test for the success state content**

In `tests/Feature/RegistrationFormTest.php`, add:

```php
public function test_success_state_shows_activity_details(): void
{
    Mail::fake();
    $activiteit = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'titel_nl' => 'Koken met kruiden',
        'locatie' => 'De Harmonie',
    ]);

    Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
        ->set('naam', 'Marie Dupont')
        ->set('email', 'marie@example.com')
        ->call('submit')
        ->assertSee('Koken met kruiden')
        ->assertSee('De Harmonie')
        ->assertSee('Je bent ingeschreven');
}
```

- [ ] **Step 3: Run the test**

```bash
php artisan test --compact --filter=test_success_state_shows_activity_details
```

Expected: PASS

- [ ] **Step 4: Run all registration tests**

```bash
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all pass

- [ ] **Step 5: Commit**

```bash
git add resources/views/livewire/registration-form.blade.php tests/Feature/RegistrationFormTest.php
git commit -m "feat: rich success state with activity details and localStorage booking"
```

---

## Task 7: Update visitor confirmation email

**Files:**
- Modify: `resources/views/mail/registratie-bevestiging.blade.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Add a test for the updated email content**

In `tests/Feature/RegistrationFormTest.php`, add:

```php
public function test_confirmation_email_has_confirmed_tone_and_activity_details(): void
{
    Mail::fake();
    $activiteit = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'titel_nl' => 'Koken met kruiden',
        'titel_fr' => 'Cuisine aux herbes',
        'locatie' => 'De Harmonie',
    ]);

    Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
        ->set('naam', 'Marie Dupont')
        ->set('email', 'marie@example.com')
        ->call('submit');

    Mail::assertSent(RegistratieBevestiging::class, function (RegistratieBevestiging $mail) use ($activiteit): bool {
        $rendered = $mail->render();
        return str_contains($rendered, 'ingeschreven')
            && str_contains($rendered, 'Koken met kruiden')
            && str_contains($rendered, 'De Harmonie')
            && str_contains($rendered, '02 203 28 48')
            && !str_contains($rendered, 'nemen snel contact');
    });
}
```

- [ ] **Step 2: Run the test to verify it fails**

```bash
php artisan test --compact --filter=test_confirmation_email_has_confirmed_tone_and_activity_details
```

Expected: FAIL — current email says "nemen snel contact"

- [ ] **Step 3: Rewrite `registratie-bevestiging.blade.php`**

```blade
<x-mail::message>
@if ($taal === 'fr')
# Confirmation d'inscription

Bonjour {{ $verzoek->naam }},

Vous êtes inscrit(e) pour :

**{{ $activiteit->titel_fr }}**
{{ ucfirst($activiteit->datum->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} · {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }}@endif
{{ $activiteit->locatie }}

Vous n'avez rien d'autre à faire — votre place est réservée.

Des questions ? Appelez-nous au **02 203 28 48** ou envoyez un e-mail à info@deharmonie.be.

À bientôt !<br>
De Harmonie
@else
# Bevestiging inschrijving

Hallo {{ $verzoek->naam }},

Je bent ingeschreven voor:

**{{ $activiteit->titel_nl }}**
{{ ucfirst($activiteit->datum->locale('nl')->isoFormat('dddd D MMMM YYYY')) }} · {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }}@endif
{{ $activiteit->locatie }}

Je hoeft niets meer te doen — je plaats is gereserveerd.

Vragen? Bel ons op **02 203 28 48** of mail naar info@deharmonie.be.

Tot dan!<br>
De Harmonie
@endif
</x-mail::message>
```

- [ ] **Step 4: Run the test**

```bash
php artisan test --compact --filter=test_confirmation_email_has_confirmed_tone_and_activity_details
```

Expected: PASS

- [ ] **Step 5: Run all registration tests**

```bash
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all pass

- [ ] **Step 6: Commit**

```bash
git add resources/views/mail/registratie-bevestiging.blade.php tests/Feature/RegistrationFormTest.php
git commit -m "feat: update confirmation email — confirmed tone, activity details, contact info"
```

---

## Task 8: Update staff notification email

**Files:**
- Modify: `app/Mail/RegistratieNotificatie.php`
- Test: `tests/Feature/RegistrationFormTest.php`

- [ ] **Step 1: Add a test for reply-to and subject with date**

In `tests/Feature/RegistrationFormTest.php`, add:

```php
public function test_staff_notification_has_reply_to_and_date_in_subject(): void
{
    Mail::fake();
    $activiteit = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'titel_nl' => 'Koken met kruiden',
        'datum' => '2026-04-18',
    ]);

    Livewire::test(RegistrationForm::class, ['activiteit' => $activiteit])
        ->set('naam', 'Marie Dupont')
        ->set('email', 'marie@example.com')
        ->call('submit');

    Mail::assertSent(RegistratieNotificatie::class, function (RegistratieNotificatie $mail): bool {
        $envelope = $mail->envelope();
        $hasReplyTo = collect($envelope->replyTo)->contains(
            fn ($address) => $address->address === 'marie@example.com'
        );
        $hasDate = str_contains($envelope->subject, 'april');
        return $hasReplyTo && $hasDate;
    });
}
```

- [ ] **Step 2: Run the test to verify it fails**

```bash
php artisan test --compact --filter=test_staff_notification_has_reply_to_and_date_in_subject
```

Expected: FAIL — no reply-to set, no date in subject

- [ ] **Step 3: Update `RegistratieNotificatie::envelope()`**

Replace the `envelope()` method in `app/Mail/RegistratieNotificatie.php`:

```php
public function envelope(): Envelope
{
    $subject = 'Nieuwe inschrijving: '
        . $this->activiteit->titel_nl
        . ' — '
        . $this->activiteit->datum->locale('nl')->isoFormat('dddd D MMMM');

    return new Envelope(
        to: [new Address(config('mail.admin_address', 'animatie@deharmonie.be'))],
        replyTo: [new Address($this->verzoek->email, $this->verzoek->naam)],
        subject: $subject,
    );
}
```

- [ ] **Step 4: Run format + the test**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=test_staff_notification_has_reply_to_and_date_in_subject
```

Expected: PASS

- [ ] **Step 5: Run all registration tests**

```bash
php artisan test --compact tests/Feature/RegistrationFormTest.php
```

Expected: all pass

- [ ] **Step 6: Commit**

```bash
git add app/Mail/RegistratieNotificatie.php tests/Feature/RegistrationFormTest.php
git commit -m "feat: staff email — reply-to registrant, date in subject"
```

---

## Task 9: Update admin — remove toggle, add infolist and ViewAction

**Files:**
- Modify: `app/Filament/Resources/DeelnameverzoekResource.php`

- [ ] **Step 1: Check Filament 4 infolist docs before writing code**

Run in the project:

```bash
php artisan tinker --execute 'echo "check docs";'
```

Then use `search-docs` MCP tool with queries: `["infolist view action", "infolist text entry"]` to confirm the correct Filament 4 API for `Infolist` and `TextEntry`. The code below uses the standard Filament 4 API — verify imports match your installed version.

- [ ] **Step 2: Replace `DeelnameverzoekResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeelnameverzoekResource\Pages;
use App\Models\Deelnameverzoek;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class DeelnameverzoekResource extends Resource
{
    protected static ?string $model = Deelnameverzoek::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Inschrijvingen';

    protected static ?string $modelLabel = 'Inschrijving';

    protected static ?string $pluralModelLabel = 'Inschrijvingen';

    protected static ?string $slug = 'deelnameverzoeken';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Persoon')->schema([
                TextEntry::make('naam')->label('Naam'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('telefoon')->label('Telefoon')->placeholder('—'),
                TextEntry::make('bericht')->label('Bericht')->placeholder('—')->columnSpanFull(),
            ])->columns(2),
            Section::make('Activiteit')->schema([
                TextEntry::make('activiteit.titel_nl')->label('Activiteit'),
                TextEntry::make('activiteit.datum')
                    ->label('Datum')
                    ->date('d/m/Y'),
                TextEntry::make('activiteit.startuur')
                    ->label('Tijdstip')
                    ->formatStateUsing(fn (string $state): string => substr($state, 0, 5)),
                TextEntry::make('created_at')->label('Ontvangen')->dateTime('d/m/Y H:i'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefoon')
                    ->label('Telefoon')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('activiteit.titel_nl')
                    ->label('Activiteit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangevraagd')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('activiteit')
                    ->relationship('activiteit', 'titel_nl'),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeelnameverzoeken::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(mixed $record): bool
    {
        return false;
    }
}
```

- [ ] **Step 3: Run format + full test suite**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact
```

Expected: all tests pass

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/DeelnameverzoekResource.php
git commit -m "feat: admin — read-only list with infolist slide-over, remove status toggle"
```

---

## Task 10: Final verification

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass, no failures

- [ ] **Step 2: Clear views and cache**

```bash
php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 3: Smoke test in browser**

Visit `https://harmonie.test` and verify:
- Activity detail page: form renders for available activity
- Activity detail page: "ingeschreven" badge appears after registering (check localStorage in devtools)
- Activity overview: badge appears on the card after registration
- Submit the form: success state shows activity name, date, location
- Admin at `/admin/deelnameverzoeken`: list loads, clicking a row opens slide-over with all details, no status column visible
