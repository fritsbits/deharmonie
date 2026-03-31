# Diensten Service Cluster Cards Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the three plain checklist columns on the Diensten page with white cards that have a coloured header (orange/green/blue) and a large decorative SVG icon per category.

**Architecture:** Pure Blade template change in a single file. The `$clusters` PHP array in `diensten.blade.php` gains `color` and `icon` fields per cluster; the markup is replaced with card chrome that matches the special activity card pattern from `activiteiten/overzicht.blade.php`.

**Tech Stack:** Laravel 13, Blade, inline CSS (project convention), Tailwind v4 not used here (existing page uses inline styles throughout)

---

### Task 1: Write a feature test for the Diensten page

No test currently exists for this page. We write one that covers the NL and FR routes and asserts the key cluster labels are visible.

**Files:**
- Create: `tests/Feature/DienstenPageTest.php`

- [ ] **Step 1: Create the test file**

```bash
php artisan make:test --phpunit DienstenPageTest
```

- [ ] **Step 2: Replace the generated content with these tests**

Open `tests/Feature/DienstenPageTest.php` and replace its contents with:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class DienstenPageTest extends TestCase
{
    public function test_nl_diensten_page_renders(): void
    {
        $response = $this->get(route('nl.diensten'));

        $response->assertStatus(200);
        $response->assertSee('Eten');
        $response->assertSee('Begeleiding');
        $response->assertSee('Thuis');
        $response->assertSee('Sociaal restaurant');
        $response->assertSee('Boodschappendienst');
    }

    public function test_fr_diensten_page_renders(): void
    {
        $response = $this->get(route('fr.diensten'));

        $response->assertStatus(200);
        $response->assertSee('Repas');
        $response->assertSee('Accompagnement');
        $response->assertSee('domicile');
        $response->assertSee('Restaurant social');
        $response->assertSee('courses');
    }
}
```

- [ ] **Step 3: Run the tests — both should pass (they test existing content)**

```bash
php artisan test --compact tests/Feature/DienstenPageTest.php
```

Expected: 2 tests, 2 passed

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/DienstenPageTest.php
git commit -m "test: add feature test for diensten page NL and FR routes"
```

---

### Task 2: Add colour and icon data to the \$clusters array

The template needs per-cluster metadata (colour hex and SVG path) to render the card headers. We add this to the existing `$clusters` PHP array in `diensten.blade.php`.

**Files:**
- Modify: `resources/views/pages/diensten.blade.php` (the `@php` block, lines ~17–68)

- [ ] **Step 1: Open `resources/views/pages/diensten.blade.php` and update the `@php` block**

Replace the entire `@php ... @endphp` block (everything from `@php` to the closing `@endphp` before the flex div) with:

```blade
@php
$clusters = app()->getLocale() === 'fr' ? [
    [
        'label_top'  => 'Repas &',
        'label_main' => 'Activités',
        'color'      => '#eb6643',
        'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
        'items' => [
            'Restaurant social, plats à emporter et livraison à domicile',
            'Restauration et location pour les habitants et les organisations locales',
            'Services, activités et sorties pour les seniors — Créatif · Détente · Culturel · Formateur · Informatif · Sportif',
        ],
    ],
    [
        'label_top'  => 'Accompagnement &',
        'label_main' => 'Soutien',
        'color'      => '#81b59c',
        'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
        'items' => [
            'Parcours dans la vie socioculturelle de Bruxelles — Service social',
            'Partenaire du réseau de soins primaires du quartier Nord',
            'Boutique de vêtements d\'occasion et retouches',
        ],
    ],
    [
        'label_top'  => 'À domicile &',
        'label_main' => 'Dans le quartier',
        'color'      => '#4679bc',
        'icon'       => '<path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z"/><path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z"/>',
        'items' => [
            'Service de courses et de transport',
            'Service de nettoyage et de bricolage',
            'Aide au Grand Nettoyage',
        ],
    ],
] : [
    [
        'label_top'  => 'Eten &',
        'label_main' => 'Activiteiten',
        'color'      => '#eb6643',
        'icon'       => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 7.5A.75.75 0 0 0 8.25 8v1.5a2.25 2.25 0 0 0 1.5 2.122v3.628a.75.75 0 0 0 1.5 0v-3.628A2.25 2.25 0 0 0 12.75 9.5V8A.75.75 0 0 0 12 7.5H9ZM15 7.5a.75.75 0 0 0-.75.75v7.5a.75.75 0 0 0 1.5 0V12.5h.75a.75.75 0 0 0 .75-.75V9a1.5 1.5 0 0 0-1.5-1.5H15Z" clip-rule="evenodd"/>',
        'items' => [
            'Sociaal restaurant, afhaal en levering aan huis',
            'Catering & Verhuur voor buurtbewoners & -organisaties',
            'Diensten, Activiteiten en Uitstappen voor Senioren — Creatief · Ontspannend · Cultureel · Vormend · Informatief · Sportief',
        ],
    ],
    [
        'label_top'  => 'Begeleiding &',
        'label_main' => 'Ondersteuning',
        'color'      => '#81b59c',
        'icon'       => '<path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd"/>',
        'items' => [
            'Wegwijs in socio-cultureel Brussel — Sociale dienst',
            'Partner in het eerstelijnszorgnetwerk in de Noordwijk',
            'Tweedehands Klerenwinkel & Retouches',
        ],
    ],
    [
        'label_top'  => 'Thuis &',
        'label_main' => 'In de buurt',
        'color'      => '#4679bc',
        'icon'       => '<path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z"/><path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z"/>',
        'items' => [
            'Boodschappendienst & Vervoersdienst',
            'Klusjesdienst & Poetsdienst',
            'Hulp bij de Grote Kuis',
        ],
    ],
];
@endphp
```

- [ ] **Step 2: Run the tests to confirm nothing broke**

```bash
php artisan test --compact tests/Feature/DienstenPageTest.php
```

Expected: 2 tests, 2 passed

- [ ] **Step 3: Commit**

```bash
git add resources/views/pages/diensten.blade.php
git commit -m "refactor: add color and icon metadata to diensten cluster data"
```

---

### Task 3: Replace the cluster markup with cards

Swap out the plain flex columns for the coloured-header card layout.

**Files:**
- Modify: `resources/views/pages/diensten.blade.php` (the flex div and `<style>` block)

- [ ] **Step 1: Replace the cluster flex div**

In `resources/views/pages/diensten.blade.php`, find and replace the entire cluster rendering block — from `<div style="display: flex; gap: 3rem; flex-wrap: wrap;">` through the closing `</div>` (the one that ends the foreach, around line 87) — with:

```blade
<div class="service-cards" style="display: flex; gap: 1.5rem;">
    @foreach ($clusters as $cluster)
        <div style="flex: 1; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.10);">

            {{-- Coloured header --}}
            <div style="background: {{ $cluster['color'] }}; padding: 1.25rem 1.5rem 1.5rem; position: relative; overflow: hidden; min-height: 90px;">
                <svg style="position: absolute; right: -14px; bottom: -18px; width: 110px; height: 110px; opacity: 0.18; transform: rotate(12deg); pointer-events: none;"
                     viewBox="0 0 24 24" fill="white" stroke="none">
                    {!! $cluster['icon'] !!}
                </svg>
                <p style="font-family: var(--font-sans); font-size: 0.7rem; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,.75); margin: 0 0 0.2rem; position: relative; z-index: 1;">
                    {{ $cluster['label_top'] }}
                </p>
                <p style="font-family: var(--font-sans); font-size: 1.125rem; font-weight: 900; color: white; margin: 0; position: relative; z-index: 1; line-height: 1.2;">
                    {{ $cluster['label_main'] }}
                </p>
            </div>

            {{-- Card body --}}
            <div style="padding: 1.25rem 1.5rem 1.5rem;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($cluster['items'] as $item)
                        <li style="display: flex; gap: 0.6rem; align-items: baseline; padding: 0.65rem 0; {{ !$loop->last ? 'border-bottom: 1px solid rgba(44,40,38,.07);' : '' }}">
                            <span style="flex-shrink: 0; color: {{ $cluster['color'] }}; font-weight: 700;">&#10003;</span>
                            <span style="font-size: 0.9375rem; color: var(--color-brand-dark); line-height: 1.45;">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    @endforeach
</div>
```

- [ ] **Step 2: Add responsive CSS**

At the bottom of `diensten.blade.php`, inside the existing `<style>` block (before `</style>`), add:

```css
.service-cards { align-items: stretch; }

@media (max-width: 767px) {
    .service-cards { flex-direction: column !important; }
}
```

- [ ] **Step 3: Run tests**

```bash
php artisan test --compact tests/Feature/DienstenPageTest.php
```

Expected: 2 tests, 2 passed

- [ ] **Step 4: Run Pint to fix any formatting**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Check the page in the browser**

Visit `https://deharmonie.test/diensten` — confirm the three cards render with coloured headers and icons.

- [ ] **Step 6: Commit**

```bash
git add resources/views/pages/diensten.blade.php
git commit -m "feat: replace diensten service columns with coloured-header cards"
```
