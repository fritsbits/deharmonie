# Language Switcher Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current "Français"/"Nederlands" nav link with a visually distinct globe icon + NL/FR toggle, separated from the menu links by a vertical divider.

**Architecture:** Single Blade component change. Desktop gets a divider + globe icon + `NL / FR` where the current locale is displayed at full opacity and the other is dimmed + underlined. Mobile dropdown gets the same pattern. No PHP logic changes — route/redirect logic is unchanged.

**Tech Stack:** Blade templates, Tailwind v4, inline SVG (Heroicons outline GlobeAltIcon), Alpine.js (mobile, unchanged)

---

### Task 1: Update the BilingualRouting test to cover the new markup

**Files:**
- Modify: `tests/Feature/BilingualRoutingTest.php`

- [ ] **Step 1: Add assertions for the new language switcher pattern**

Open `tests/Feature/BilingualRoutingTest.php`. Replace the existing `test_nav_language_switcher_link_present` test and add two new tests:

```php
public function test_nav_language_switcher_link_present(): void
{
    $response = $this->get('/');
    $response->assertSee('set-locale', false);
}

public function test_nl_nav_shows_fr_as_link(): void
{
    $response = $this->get('/');
    // FR is the clickable "other" language on the NL site
    $response->assertSee('set-locale', false);
    $response->assertSee('>FR<', false);
    // NL appears as a non-linked span
    $response->assertSee('>NL<', false);
}

public function test_fr_nav_shows_nl_as_link(): void
{
    $response = $this->get('/fr');
    $response->assertSee('>NL<', false);
    $response->assertSee('>FR<', false);
}
```

- [ ] **Step 2: Run the new tests to verify they fail**

```bash
php artisan test --compact --filter=test_nl_nav_shows_fr_as_link
```

Expected: FAIL — the current nav outputs "Français", not `>FR<`.

---

### Task 2: Update the nav component — desktop

**Files:**
- Modify: `resources/views/components/nav.blade.php`

The current desktop language switcher block (lines 32–48) looks like:

```blade
@php
    $targetLocale  = app()->getLocale() === 'nl' ? 'fr' : 'nl';
    ...
@endphp
<a href="{{ route('set-locale', [...]) }}"
   class="font-semibold hover:opacity-75 transition-opacity"
   style="color: white; font-size: 1.125rem; opacity: 0.75;">
   {{ __('nav.language_switch') }}
</a>
```

- [ ] **Step 1: Replace the desktop language switcher**

Replace those lines with:

```blade
@php
    $targetLocale = app()->getLocale() === 'nl' ? 'fr' : 'nl';
    $currentLocaleLabel = strtoupper(app()->getLocale());
    $targetLocaleLabel  = strtoupper($targetLocale);
    $currentName = request()->route()?->getName();
    $targetRoute = $currentName
        ? preg_replace('/^(nl|fr)\./', $targetLocale . '.', $currentName)
        : $targetLocale . '.home';
    try {
        $targetUrl = route($targetRoute, request()->route()?->parameters() ?? []);
    } catch (\Exception) {
        $targetUrl = route($targetLocale . '.home');
    }
@endphp
{{-- Divider --}}
<span aria-hidden="true" style="width: 1px; height: 1.25rem; background: rgba(255,255,255,0.35); margin: 0 0.25rem;"></span>
{{-- Language toggle --}}
<div class="flex items-center gap-1.5" style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 600;">
    {{-- Globe icon (Heroicons outline GlobeAltIcon) --}}
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
         style="color: white; opacity: 0.75; flex-shrink: 0;">
        <circle cx="12" cy="12" r="10"/>
        <line x1="2" y1="12" x2="22" y2="12"/>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
    </svg>
    {{-- Current locale (not a link) --}}
    <span style="color: white; opacity: 1;">{{ $currentLocaleLabel }}</span>
    {{-- Separator --}}
    <span aria-hidden="true" style="color: white; opacity: 0.4; font-size: 0.75rem;">/</span>
    {{-- Other locale (link) --}}
    <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
       style="color: white; opacity: 0.6; text-decoration: underline;"
       class="hover:opacity-90 transition-opacity">{{ $targetLocaleLabel }}</a>
</div>
```

- [ ] **Step 2: Run the failing tests to verify they now pass**

```bash
php artisan test --compact --filter=test_nl_nav_shows_fr_as_link
php artisan test --compact --filter=test_fr_nav_shows_nl_as_link
```

Expected: both PASS.

- [ ] **Step 3: Run the full BilingualRoutingTest**

```bash
php artisan test --compact tests/Feature/BilingualRoutingTest.php
```

Expected: all tests PASS.

---

### Task 3: Update the nav component — mobile dropdown

**Files:**
- Modify: `resources/views/components/nav.blade.php`

The current mobile language link (line ~67) looks like:

```blade
<a href="{{ route('set-locale', ...) }}" class="block font-semibold"
   style="color: white; opacity: 0.75; padding: 1rem 0; font-size: 1.125rem; ...">
   {{ __('nav.language_switch') }}
</a>
```

The `$targetLocale`, `$targetUrl`, `$currentLocaleLabel`, and `$targetLocaleLabel` variables are already set from the `@php` block above (Task 2 moved it above both desktop and mobile sections — verify placement covers the mobile section too, or duplicate the `@php` block inside the mobile `x-data` div if needed).

- [ ] **Step 1: Replace the mobile language link**

Replace the mobile language `<a>` tag with:

```blade
{{-- Mobile language toggle --}}
<div class="flex items-center gap-2" style="padding: 1rem 0; border-top: 1px solid rgba(255,255,255,0.15);">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
         style="color: white; opacity: 0.7; flex-shrink: 0;">
        <circle cx="12" cy="12" r="10"/>
        <line x1="2" y1="12" x2="22" y2="12"/>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
    </svg>
    <span style="color: white; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans);">{{ $currentLocaleLabel }}</span>
    <span aria-hidden="true" style="color: white; opacity: 0.4; font-size: 0.875rem;">/</span>
    <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
       style="color: white; opacity: 0.65; font-weight: 600; font-size: 1.125rem; font-family: var(--font-sans); text-decoration: underline;"
       class="hover:opacity-90 transition-opacity">{{ $targetLocaleLabel }}</a>
</div>
```

Note: remove the `border-top` from the surrounding `<a>` if it had one — the new `<div>` carries its own border.

- [ ] **Step 2: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests PASS.

- [ ] **Step 3: Run Pint to format changed PHP files**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 4: Commit**

Write the commit message to `/tmp/lang-switcher-commit.txt`:
```
feat: redesign language switcher with globe icon and NL/FR toggle

Replaces the full-word "Français"/"Nederlands" link with a visually
distinct control: vertical divider + globe icon + NL/FR where the
current locale is solid and the other is dimmed + underlined.
Applied to both desktop nav and mobile dropdown.
```

Then commit:
```bash
git add resources/views/components/nav.blade.php tests/Feature/BilingualRoutingTest.php
git commit -F /tmp/lang-switcher-commit.txt
```
