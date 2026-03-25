# Homepage Hero Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current hero (overlapping photos + bullets) and standalone agenda section with a text-only hero and three full-width alternating photo/text sections, embedding the Livewire activity list inside the activities section.

**Architecture:** The homepage view (`activiteiten/index.blade.php`) is rewritten in place. The Livewire `ActivityFilter` component is embedded in section 2's right column with a limit of 5. An Alpine.js carousel handles the three activity photos. The opening hours section is untouched.

**Tech Stack:** Laravel 13, Blade, Livewire 3, Alpine.js (already available via Livewire), inline CSS (Tailwind v4 scan limitations)

---

## File Map

| File | Change |
|---|---|
| `public/images/photo-restaurant.jpg` | **Add** — restaurant crowd photo (Facebook) |
| `public/images/photo-party.jpg` | **Add** — cultural party/dancing photo |
| `public/images/photo-cake.jpg` | **Add** — cake celebration photo |
| `public/images/photo-samen.jpg` | **Add** — laughing couple photo |
| `public/images/photo-thumbsup.jpg` | **Add** — thumbs-up / group photo |
| `app/Livewire/ActivityFilter.php` | Modify — limit 10 → 5 |
| `resources/views/livewire/activity-filter.blade.php` | Modify — green buttons, remove top padding |
| `resources/views/activiteiten/index.blade.php` | Rewrite hero + agenda sections |
| `app/Http/Controllers/ActivityController.php` | Modify — remove unused `$activiteiten` from `home()` |
| `tests/Feature/ActivityControllerTest.php` | Modify — update homepage test to reflect new structure |

---

## Task 1: Add required photos

**Files:**
- Add: `public/images/photo-restaurant.jpg`
- Add: `public/images/photo-party.jpg`
- Add: `public/images/photo-cake.jpg`
- Add: `public/images/photo-samen.jpg`
- Add: `public/images/photo-thumbsup.jpg`

- [ ] **Step 1: Copy photos from Desktop (or wherever they live) to `public/images/`**

```bash
# Example — adjust source paths to wherever the Facebook photos are saved
cp ~/Desktop/photo-restaurant.jpg /Users/frederikvincx/Herd/harmonie/public/images/
cp ~/Desktop/photo-party.jpg       /Users/frederikvincx/Herd/harmonie/public/images/
cp ~/Desktop/photo-cake.jpg        /Users/frederikvincx/Herd/harmonie/public/images/
cp ~/Desktop/photo-samen.jpg       /Users/frederikvincx/Herd/harmonie/public/images/
cp ~/Desktop/photo-thumbsup.jpg    /Users/frederikvincx/Herd/harmonie/public/images/
```

- [ ] **Step 2: Verify files exist**

```bash
ls -lh /Users/frederikvincx/Herd/harmonie/public/images/photo-*.jpg
```

Expected: five `.jpg` files listed.

- [ ] **Step 3: Commit**

```bash
cd /Users/frederikvincx/Herd/harmonie
git add public/images/photo-restaurant.jpg public/images/photo-party.jpg public/images/photo-cake.jpg public/images/photo-samen.jpg public/images/photo-thumbsup.jpg
git commit -m "feat: add homepage section photos (restaurant, party, cake, samen, thumbsup)"
```

---

## Task 2: Change ActivityFilter limit from 10 to 5

**Files:**
- Modify: `app/Livewire/ActivityFilter.php:19`

The component currently returns up to 10 activities. The homepage section only has room for 5.

- [ ] **Step 1: Write the failing test**

Open `tests/Feature/ActivityControllerTest.php` and add:

```php
public function test_homepage_activity_list_shows_at_most_five(): void
{
    // Create 7 upcoming published activities
    Activiteit::factory()->count(7)->create([
        'status' => 'gepubliceerd',
        'datum'  => now()->format('Y-m-d'),
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);

    // Livewire renders initial HTML server-side.
    // With limit 10 the component shows all 7; with limit 5 only 5 are rendered.
    // We assert the page does NOT contain the 6th or 7th activity's title.
    $activities = \App\Models\Activiteit::orderBy('datum')->orderBy('startuur')->get();
    $response->assertSee($activities[4]->titel_nl);      // 5th — should appear
    $response->assertDontSee($activities[5]->titel_nl);  // 6th — should NOT appear
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
cd /Users/frederikvincx/Herd/harmonie
php artisan test --filter=test_homepage_activity_list_shows_at_most_five
```

Expected: FAIL — the 6th activity's title IS seen (limit is still 10).

- [ ] **Step 3: Change the limit in ActivityFilter.php**

In `app/Livewire/ActivityFilter.php`, change line 19:

```php
// Before
->limit(10)

// After
->limit(5)
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --filter=test_homepage_activity_list_shows_at_most_five
```

Expected: PASS.

- [ ] **Step 5: Run all tests to make sure nothing broke**

```bash
php artisan test
```

Expected: all tests pass.

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/ActivityFilter.php tests/Feature/ActivityControllerTest.php
git commit -m "feat: reduce ActivityFilter limit to 5 for homepage section"
```

---

## Task 3: Update activity-filter view for homepage embedding

**Files:**
- Modify: `resources/views/livewire/activity-filter.blade.php`

The spec says the "Alle activiteiten" / "Toutes les activités" buttons become the section CTA and should use green (`var(--color-brand-green)`), not blue.

- [ ] **Step 1: Update button colors in `activity-filter.blade.php`**

Change both button background colors from `var(--color-brand-blue)` to `var(--color-brand-green)` (lines 57 and 61):

```html
{{-- Bottom buttons --}}
<div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
    <a href="{{ route('nl.activiteiten.index') }}"
       style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
        Alle activiteiten
    </a>
    <a href="{{ route('fr.activiteiten.index') }}"
       style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
        Toutes les activités
    </a>
</div>
```

- [ ] **Step 2: Add bottom padding to the activity-filter view**

The component is embedded inside a column. Add `padding-bottom: 2rem` to the bottom buttons div so there's breathing room at the base of the section:

```html
<div style="margin-top: 1.5rem; padding-bottom: 2rem; display: flex; gap: 0.75rem;">
```

- [ ] **Step 3: Add a border-bottom between activity rows**

Each row should have a separator. The current view has no dividers. Update each activity row's `<a>` to include `border-bottom: 1px solid rgba(216,211,210,0.7)` and remove it from the last item:

Wrap the forelse loop so each `<a>` gets:
```html
style="... border-bottom: 1px solid rgba(216,211,210,0.7);"
```

And the last item:
```blade
@if (!$loop->last)
    style="... border-bottom: 1px solid rgba(216,211,210,0.7);"
@else
    style="..."
@endif
```

Full updated `activity-filter.blade.php`:

```blade
<div>
    {{-- Activity list --}}
    <div>
        @php
            $thumbColors = ['#f3dbd5','#d4e8df','#d5e0f0','#f5e8d3','#dde7d5','#e8d9ef','#d9e8f0'];
        @endphp
        @forelse ($this->activiteiten as $activiteit)
            @php
                $colorIdx = abs(crc32($activiteit->slug ?? '')) % count($thumbColors);
                $thumbColor = $thumbColors[$colorIdx];
            @endphp
            <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
               style="display: flex; align-items: center; gap: 1rem; padding: 0.65rem 0; text-decoration: none; opacity: {{ $activiteit->status->value === 'geannuleerd' ? '0.5' : '1' }}; {{ !$loop->last ? 'border-bottom: 1px solid rgba(216,211,210,0.7);' : '' }}">

                {{-- Thumbnail --}}
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 6px; overflow: hidden; background-color: {{ $thumbColor }};">
                    @if ($activiteit->getFirstMediaUrl('afbeelding'))
                        <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                             alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <p style="font-weight: 700; font-size: 0.85rem; line-height: 1.2; color: var(--color-brand-dark); font-family: var(--font-sans); margin: 0;">
                            {{ $activiteit->titel }}
                        </p>
                        @if ($activiteit->status->value === 'geannuleerd')
                            <span style="flex-shrink: 0; font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background-color: #fde8e3; color: #c0392b;">
                                &times;
                            </span>
                        @endif
                    </div>
                    <p style="font-size: 0.75rem; margin: 0.1rem 0 0; color: var(--color-brand-muted);">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')) }}
                        {{ $activiteit->datum->format('j/n') }}
                        om {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur)
                            &ndash; {{ substr($activiteit->einduur, 0, 5) }}
                        @endif
                        &middot; {{ $activiteit->locatie }}
                    </p>
                </div>

            </a>
        @empty
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 0.9rem;">
                {{ app()->getLocale() === 'fr' ? 'Pas d\'activités prévues.' : 'Geen activiteiten gepland.' }}
            </p>
        @endforelse
    </div>

    {{-- Bottom buttons / section CTA --}}
    <div style="margin-top: 1.5rem; padding-bottom: 2rem; display: flex; gap: 0.75rem;">
        <a href="{{ route('nl.activiteiten.index') }}"
           style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
            Alle activiteiten
        </a>
        <a href="{{ route('fr.activiteiten.index') }}"
           style="font-size: 0.9rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--color-brand-green); color: white; text-decoration: none; font-family: var(--font-sans);">
            Toutes les activités
        </a>
    </div>
</div>
```

- [ ] **Step 4: Run tests**

```bash
php artisan test
```

Expected: all tests pass.

- [ ] **Step 5: Commit**

```bash
git add resources/views/livewire/activity-filter.blade.php
git commit -m "feat: update activity-filter view — green CTAs, row dividers, compact thumbnails"
```

---

## Task 4: Rewrite homepage — hero + three sections

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`
- Modify: `app/Http/Controllers/ActivityController.php`

Replace the hero (photo collage + bullets) and the standalone AGENDA section with a text-only hero and three alternating photo/text sections. Section 2 embeds the Livewire activity filter.

Remove the now-unused `$activiteiten` variable from the controller's `home()` method.

- [ ] **Step 1: Update the `home()` controller method**

In `app/Http/Controllers/ActivityController.php`, simplify `home()` — the Livewire component now handles its own data:

```php
public function home()
{
    return view('activiteiten.index');
}
```

- [ ] **Step 2: Write the failing test for the new homepage structure**

In `tests/Feature/ActivityControllerTest.php`, update `test_homepage_shows_published_activities` to verify the Livewire component is present and the old agenda section is gone:

```php
public function test_homepage_shows_published_activities(): void
{
    $gepubliceerd = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'datum'  => now()->format('Y-m-d'),
    ]);
    $concept = Activiteit::factory()->create([
        'status' => 'concept',
        'datum'  => now()->format('Y-m-d'),
    ]);

    $response = $this->get('/');
    // Activity appears via Livewire server-side render
    $response->assertSee($gepubliceerd->titel_nl);
    $response->assertDontSee($concept->titel_nl);
    // New text-only hero content
    $response->assertSee('Noordwijk · Brussel');
    $response->assertSee('Dienstencentrum');
    $response->assertSee('Quartier Noordwijk');
    // Three sections present
    $response->assertSee('Elke dag samen aan tafel');
    $response->assertSee('Creatief, cultureel en sportief');
    $response->assertSee('Ook hulp waar u het nodig heeft');
    // Old standalone AGENDA section is gone
    $response->assertDontSee('Volgende activiteiten');
}
```

- [ ] **Step 3: Run the test to verify it fails**

```bash
php artisan test --filter=test_homepage_shows_published_activities
```

Expected: FAIL — new section headings not found, "Volgende activiteiten" still present.

- [ ] **Step 4: Rewrite `index.blade.php`**

Replace the entire content of `resources/views/activiteiten/index.blade.php` with:

```blade
@extends('layouts.app')

@section('title', app()->getLocale() === 'fr' ? 'Accueil' : 'Home')

@section('content')

{{-- HERO: text only --}}
<section style="background-color: white; border-bottom: 1px solid #ebe8e5;">
    <div style="max-width: 64rem; margin: 0 auto; padding: 3rem 1.5rem 2.5rem;">
        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.4rem;">
            Noordwijk · Brussel
        </p>
        <h1 style="font-family: var(--font-sans); font-size: 2.8rem; font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
            Dienstencentrum<br>Restaurant Social
        </h1>
        <h2 style="font-family: var(--font-sans); font-size: 1.6rem; font-weight: 900; color: var(--color-brand-green); line-height: 1.2;">
            Quartier Noordwijk
        </h2>
    </div>
</section>

{{-- SECTION 1: Restaurant — photo left, text right --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; min-height: 320px;">
        <div style="flex: 0 0 42%; overflow: hidden; position: relative;">
            <img src="{{ asset('images/photo-restaurant.jpg') }}" alt="Sociaal restaurant"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; padding: 2rem; display: flex; flex-direction: column; background: white;">
            <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-orange); margin-bottom: 0.5rem;">
                Sociaal restaurant · Restaurant social
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.6rem;">
                Elke dag samen aan tafel
            </h2>
            <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
                <strong>Dagschotels</strong> aan verminderd tarief voor senioren. Afhaal en levering aan huis mogelijk.
            </p>
            <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic; margin-bottom: 1.25rem;">
                Plat du jour à un tarif réduit pour les seniors. Emporter et livraison à domicile.
            </p>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--color-brand-blue); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1.1rem; border-radius: 5px; text-decoration: none; align-self: flex-start; margin-top: auto;">
                Weekmenu de la Semaine →
            </a>
        </div>
    </div>
</section>

{{-- SECTION 2: Activities — carousel right, content+list left --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; flex-direction: row-reverse; min-height: 320px;">

        {{-- Photo carousel (right) --}}
        @php $carouselPhotos = ['photo-party.jpg', 'photo-cake.jpg', 'photo-thumbsup.jpg']; @endphp
        <div x-data="{ current: 0 }"
             style="flex: 0 0 42%; position: relative; overflow: hidden; min-height: 320px;">
            @foreach ($carouselPhotos as $idx => $photo)
                <img src="{{ asset('images/' . $photo) }}"
                     alt="Activiteiten"
                     x-show="current === {{ $idx }}"
                     style="width: 100%; height: 100%; object-fit: cover; display: block; position: absolute; inset: 0;">
            @endforeach

            {{-- Prev arrow --}}
            <button @click="current = (current - 1 + {{ count($carouselPhotos) }}) % {{ count($carouselPhotos) }}"
                    style="position: absolute; left: 0.6rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); color: var(--color-brand-dark); width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; z-index: 2;">
                ‹
            </button>
            {{-- Next arrow --}}
            <button @click="current = (current + 1) % {{ count($carouselPhotos) }}"
                    style="position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); color: var(--color-brand-dark); width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; z-index: 2;">
                ›
            </button>
            {{-- Dots --}}
            <div style="position: absolute; bottom: 0.75rem; left: 0; right: 0; display: flex; justify-content: center; gap: 0.4rem; z-index: 2;">
                @foreach ($carouselPhotos as $idx => $photo)
                    <span @click="current = {{ $idx }}"
                          :style="current === {{ $idx }} ? 'opacity:1' : 'opacity:0.5'"
                          style="width: 7px; height: 7px; border-radius: 50%; background: white; display: block; cursor: pointer;"></span>
                @endforeach
            </div>
        </div>

        {{-- Header + live activity list (left) --}}
        <div style="flex: 1; display: flex; flex-direction: column; background: #f5f2ef;">
            <div style="padding: 2rem 2rem 1rem;">
                <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-green); margin-bottom: 0.5rem;">
                    Activiteiten · Activités
                </p>
                <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.5rem;">
                    Creatief, cultureel en sportief
                </h2>
                <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.2rem;">
                    <strong>Activiteiten &amp; diensten</strong> in ons centrum en bij u thuis.
                </p>
                <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic;">
                    Des activités dans notre centre et chez vous. Créatif, culturel, formateur.
                </p>
            </div>
            <div style="padding: 0 2rem; flex: 1;">
                @livewire('activity-filter')
            </div>
        </div>

    </div>
</section>

{{-- SECTION 3: Services — photo left, text right --}}
<section style="border-top: 1px solid rgba(216,211,210,0.5);">
    <div style="display: flex; min-height: 320px;">
        <div style="flex: 0 0 42%; overflow: hidden; position: relative;">
            <img src="{{ asset('images/photo-samen.jpg') }}" alt="Diensten"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="flex: 1; padding: 2rem; display: flex; flex-direction: column; background: #f0efed;">
            <p style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-blue); margin-bottom: 0.5rem;">
                Diensten · Services
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 1.35rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.6rem;">
                Ook hulp waar u het nodig heeft
            </h2>
            <p style="font-size: 0.95rem; line-height: 1.55; color: var(--color-brand-dark); margin-bottom: 0.35rem;">
                <strong>Partner</strong> voor iedereen met een hart voor onze buurt. Boodschappen, vervoer, poetswerk en meer.
            </p>
            <p style="font-size: 0.88rem; line-height: 1.5; color: var(--color-brand-muted); font-style: italic; margin-bottom: 1.25rem;">
                Partenaire pour tout le monde. Courses, transport, nettoyage et petites réparations.
            </p>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1.1rem; border-radius: 5px; text-decoration: none; align-self: flex-start; margin-top: auto;">
                Onze diensten →
            </a>
        </div>
    </div>
</section>

{{-- OPENING HOURS — unchanged --}}
<section id="contact" style="background-color: white; position: relative; overflow: hidden;">
    <img src="{{ asset('images/header-illustration.png') }}"
         id="opening-hours-illustration"
         alt=""
         style="position: absolute; right: 0; top: 0; height: 100%; width: auto; pointer-events: none; user-select: none;">
    <div class="max-w-5xl mx-auto" style="position: relative; z-index: 1; padding: 4rem 1.5rem;">
        <div style="max-width: 36rem;">
            <p style="color: var(--color-brand-green); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.15rem; font-family: var(--font-sans); letter-spacing: 0.06em; text-transform: uppercase;">
                OPENINGSUREN
            </p>
            <h2 style="font-family: var(--font-sans); font-size: 2.25rem; font-weight: 800; color: var(--color-brand-dark); margin-bottom: 1.75rem;">
                {{ app()->getLocale() === 'fr' ? 'Venez nous rendre visite' : 'Kom eens langs' }}
            </h2>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    10u – 16u30, maandag tot vrijdag
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 1.125rem; color: var(--color-brand-dark); font-weight: 600;">
                    <img src="{{ asset('images/icon-clock.svg') }}" alt="" style="width: 26px; height: 26px; flex-shrink: 0;">
                    10u – 14u, zaterdag
                </div>
            </div>
            <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 2rem;">
                Kom voor een lekker maaltijd of voor de activiteiten en uitstappen. We geven je graag ook meer info over diensten zoals vervoer, poetsdienst (ook ruilen wassen), boodschappen, kleine herstellingen, wassen en strijken en maaltijden aan huis.
            </p>
            <p style="margin-bottom: 0.4rem;">
                <a href="tel:0220328048" style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: none;">
                    02/203.28.48
                </a>
            </p>
            <p style="font-size: 1.125rem; color: var(--color-brand-blue);">
                <a href="mailto:info@deharmonie.be" style="text-decoration: none; color: inherit;">info@deharmonie.be</a>
            </p>
        </div>
    </div>
</section>

<style>
@media (max-width: 1023px) {
    #opening-hours-illustration { display: none; }
}
</style>

@endsection
```

- [ ] **Step 5: Run the failing test**

```bash
php artisan test --filter=test_homepage_shows_published_activities
```

Expected: PASS.

- [ ] **Step 6: Run all tests**

```bash
php artisan test
```

Expected: all pass. If `test_homepage_shows_published_activities` was checking for the old "Volgende activiteiten" heading and fails, update the test per the code in Step 2.

- [ ] **Step 7: Clear view cache and verify in browser**

```bash
php artisan view:clear && php artisan cache:clear
```

Visit `https://harmonie.test` — confirm:
- Text-only hero (eyebrow green, H1 dark, H2 green)
- Section 1: restaurant photo left, text right, blue weekmenu button
- Section 2: carousel right (party/cake/thumbsup with arrows + dots), activity list left on #f5f2ef bg, green buttons
- Section 3: couple photo left, text right on #f0efed bg, orange diensten button
- Opening hours section unchanged

- [ ] **Step 8: Commit**

```bash
git add resources/views/activiteiten/index.blade.php app/Http/Controllers/ActivityController.php tests/Feature/ActivityControllerTest.php
git commit -m "feat: homepage hero redesign — text-only hero, three alternating sections, embedded activity carousel"
```

---

## Task 5: Verify and take screenshot

- [ ] **Step 1: Run the full test suite one final time**

```bash
php artisan test
```

Expected: all green.

- [ ] **Step 2: Take a screenshot for visual review**

```bash
node /Users/frederikvincx/Herd/harmonie/scripts/screenshot.cjs
```

Or use the `/tmp` pattern from global CLAUDE.md:

```js
// /tmp/screenshot-homepage.cjs
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();
  await page.goto('https://harmonie.test');
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: '/tmp/homepage-hero.png', fullPage: true });
  console.log('/tmp/homepage-hero.png');
  await browser.close();
})();
```

```bash
node /tmp/screenshot-homepage.cjs
```

Review the screenshot. Confirm the three-section layout matches the approved concept v3.
