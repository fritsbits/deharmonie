# Fotomapping & Foto-optimalisatie Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Vervang verkeerd geplaatste foto's, voeg 12 nieuwe Facebook-foto's toe als geoptimaliseerde .webp bestanden, pas alle views aan, en ruim verouderde bestanden op.

**Architecture:** Alle foto's in `public/images/` zijn statische assets. Conversie gebeurt via `sips` (macOS ingebouwd). Views verwijzen direct naar `asset('images/filename.webp')`. Geen database of media library betrokken — dit is puur bestandssysteem + Blade templates.

**Tech Stack:** macOS `sips` (webp conversie), Blade views, PHPUnit feature tests

---

## Bestandsoverzicht

**Nieuwe bestanden (aanmaken):**
- `public/images/photo-gebouw.webp` — vervangt bestaand (zelfde naam, nieuwe inhoud)
- `public/images/photo-petanque.webp` — van `photo-petanque.jpg` (al aanwezig)
- `public/images/photo-handwerk.webp`
- `public/images/photo-uitstap.webp`
- `public/images/photo-muzikanten.webp`
- `public/images/photo-verjaardag.webp`
- `public/images/photo-harmonie-bus.webp`
- `public/images/photo-chef-taart-2.webp`
- `public/images/photo-gemeenschap.webp`
- `public/images/photo-krijtbord.webp`
- `public/images/photo-keukenteam.webp`
- `public/images/photo-verjaardag-team.webp`
- `public/images/grote-kuis.webp` — van `grote-kuis.jpg` (al aanwezig)

**Views aanpassen:**
- `resources/views/activiteiten/index.blade.php` (2 wijzigingen)
- `resources/views/activiteiten/overzicht.blade.php` (5 wijzigingen)
- `resources/views/pages/diensten.blade.php` (2 wijzigingen)
- `resources/views/pages/weekmenu.blade.php` (2 wijzigingen)

**Nieuwe testfile:**
- `tests/Feature/ImageAssetsTest.php`

**Verwijderen (laatste stap):**
- `public/images/photo-cake.jpg`
- `public/images/grote-kuis.jpg`
- `public/images/photo-chef-taart.webp`
- `public/images/photo-buiten-event.jpg`
- `public/images/photo-buiten-event.webp`
- `public/images/photo-petanque.jpg`

---

### Task 1: Schrijf de image assets test (failing)

**Files:**
- Create: `tests/Feature/ImageAssetsTest.php`

- [ ] **Stap 1: Maak de testfile aan**

```bash
php artisan make:test --phpunit ImageAssetsTest
```

- [ ] **Stap 2: Vervang de inhoud van `tests/Feature/ImageAssetsTest.php`**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageAssetsTest extends TestCase
{
    /**
     * Alle foto's die in views worden gebruikt moeten bestaan als bestand.
     * Als een bestand ontbreekt, faalt deze test — voeg het bestand toe.
     */
    public function test_all_referenced_images_exist(): void
    {
        $images = [
            // Homepage
            'photo-groep-tafel.webp',
            'photo-party.webp',
            'photo-groep-actief.webp',
            'photo-thumbsup.webp',
            'photo-samen.webp',
            'photo-visitors-2.webp',
            'photo-gebouw.webp',
            // Activiteiten overzicht
            'photo-petanque.webp',
            'photo-handwerk.webp',
            'photo-uitstap.webp',
            'photo-muzikanten.webp',
            'photo-verjaardag.webp',
            // Diensten
            'photo-harmonie-bus.webp',
            'photo-onthaal.webp',
            'photo-keuken-chefs.webp',
            'grote-kuis.webp',
            // Weekmenu
            'photo-chef-taart-2.webp',
            'photo-restaurant-bord.webp',
            'photo-feest-2.webp',
        ];

        foreach ($images as $image) {
            $this->assertFileExists(
                public_path('images/' . $image),
                "Afbeelding ontbreekt: public/images/{$image}"
            );
        }
    }

    /**
     * Bestanden die verwijderd moeten worden mogen niet meer bestaan.
     * Als een bestand nog bestaat, is de opruimstap nog niet uitgevoerd.
     */
    public function test_deprecated_images_are_removed(): void
    {
        $deprecated = [
            'photo-cake.jpg',
            'grote-kuis.jpg',
            'photo-chef-taart.webp',
            'photo-buiten-event.webp',
            'photo-petanque.jpg',
        ];

        foreach ($deprecated as $image) {
            $this->assertFileDoesNotExist(
                public_path('images/' . $image),
                "Verouderd bestand nog aanwezig: public/images/{$image}"
            );
        }
    }
}
```

- [ ] **Stap 3: Draai de test — verwacht FAIL**

```bash
php artisan test --compact --filter=ImageAssetsTest
```

Verwacht: meerdere failures over ontbrekende .webp bestanden én aanwezige deprecated bestanden.

---

### Task 2: Converteer en kopieer nieuwe foto's vanuit Downloads

**Files:**
- Create: `public/images/photo-gebouw.webp` (vervangt bestaand)
- Create: `public/images/photo-handwerk.webp`
- Create: `public/images/photo-uitstap.webp`
- Create: `public/images/photo-muzikanten.webp`
- Create: `public/images/photo-verjaardag.webp`
- Create: `public/images/photo-harmonie-bus.webp`
- Create: `public/images/photo-chef-taart-2.webp`
- Create: `public/images/photo-gemeenschap.webp`
- Create: `public/images/photo-krijtbord.webp`
- Create: `public/images/photo-keukenteam.webp`
- Create: `public/images/photo-verjaardag-team.webp`

- [ ] **Stap 1: Converteer alle Facebook-foto's naar webp**

`sips` converteert naar webp op max 1200px breed, 80% kwaliteit. Als de foto kleiner is dan 1200px wordt hij niet vergroot.

```bash
FOTOS=~/Downloads/de-harmonie/fotos
OUT=public/images

sips -s format webp -s formatOptions 80 "$FOTOS/481072436_1039282111573716_2573563843874455296_n.jpg" --resampleWidth 1200 --out "$OUT/photo-gebouw.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/482028050_1046909067477687_126727110874941287_n.jpg" --resampleWidth 1200 --out "$OUT/photo-handwerk.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/481665227_1044615937707000_1068418230202194301_n.jpg" --resampleWidth 1200 --out "$OUT/photo-uitstap.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/483510180_1050352053800055_1523109867317250034_n.jpg" --resampleWidth 1200 --out "$OUT/photo-muzikanten.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/482218580_1048435923991668_1324350017231061311_n.jpg" --resampleWidth 1200 --out "$OUT/photo-verjaardag.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/484524442_1052041683631092_723256461942461732_n.jpg" --resampleWidth 1200 --out "$OUT/photo-harmonie-bus.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/484750962_1053730803462180_4663615693931062014_n.jpg" --resampleWidth 1200 --out "$OUT/photo-chef-taart-2.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/483963718_1049655307203063_7868760229345822322_n.jpg" --resampleWidth 1200 --out "$OUT/photo-gemeenschap.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/484023936_1048307644004496_3444805199936742840_n.jpg" --resampleWidth 1200 --out "$OUT/photo-krijtbord.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/482345577_1048641877304406_4982695947235288809_n.jpg" --resampleWidth 1200 --out "$OUT/photo-keukenteam.webp"

sips -s format webp -s formatOptions 80 "$FOTOS/484935823_1053730410128886_2048083724632229545_n.jpg" --resampleWidth 1200 --out "$OUT/photo-verjaardag-team.webp"
```

- [ ] **Stap 2: Controleer de bestandsgroottes**

```bash
ls -lh public/images/photo-gebouw.webp public/images/photo-handwerk.webp public/images/photo-uitstap.webp public/images/photo-muzikanten.webp public/images/photo-verjaardag.webp public/images/photo-harmonie-bus.webp public/images/photo-chef-taart-2.webp public/images/photo-gemeenschap.webp public/images/photo-krijtbord.webp public/images/photo-keukenteam.webp public/images/photo-verjaardag-team.webp
```

Verwacht: alle bestanden aanwezig, elk kleiner dan 200KB.

- [ ] **Stap 3: Commit**

```bash
git add public/images/photo-gebouw.webp public/images/photo-handwerk.webp public/images/photo-uitstap.webp public/images/photo-muzikanten.webp public/images/photo-verjaardag.webp public/images/photo-harmonie-bus.webp public/images/photo-chef-taart-2.webp public/images/photo-gemeenschap.webp public/images/photo-krijtbord.webp public/images/photo-keukenteam.webp public/images/photo-verjaardag-team.webp
git commit -m "feat: add new webp photos from Facebook library"
```

---

### Task 3: Converteer bestaande .jpg bestanden en heroptimaliseer feest-2

**Files:**
- Create: `public/images/photo-petanque.webp`
- Create: `public/images/grote-kuis.webp`
- Modify: `public/images/photo-feest-2.webp` (herexporteren)

- [ ] **Stap 1: Converteer photo-petanque.jpg → .webp**

```bash
sips -s format webp -s formatOptions 80 public/images/photo-petanque.jpg --resampleWidth 1200 --out public/images/photo-petanque.webp
```

- [ ] **Stap 2: Converteer grote-kuis.jpg → .webp**

```bash
sips -s format webp -s formatOptions 80 public/images/grote-kuis.jpg --resampleWidth 1200 --out public/images/grote-kuis.webp
```

- [ ] **Stap 3: Herexporteer photo-feest-2.webp (636KB → ~120KB)**

Zoek het originele .jpg op (als bronbestand voor herexport):

```bash
sips -s format webp -s formatOptions 80 public/images/photo-feest-2.jpg --resampleWidth 1200 --out public/images/photo-feest-2.webp
```

Als `photo-feest-2.jpg` niet meer bestaat, gebruik het huidige .webp als bron:

```bash
# Controleer eerst of .jpg bestaat
ls -lh public/images/photo-feest-2.jpg 2>/dev/null || echo "Geen .jpg — gebruik .webp als bron"

# Als .jpg bestaat:
sips -s format webp -s formatOptions 80 public/images/photo-feest-2.jpg --resampleWidth 1200 --out public/images/photo-feest-2.webp

# Als enkel .webp beschikbaar:
cp public/images/photo-feest-2.webp /tmp/feest-2-backup.webp
sips -s format webp -s formatOptions 80 /tmp/feest-2-backup.webp --resampleWidth 1200 --out public/images/photo-feest-2.webp
```

- [ ] **Stap 4: Controleer resultaten**

```bash
ls -lh public/images/photo-petanque.webp public/images/grote-kuis.webp public/images/photo-feest-2.webp
```

Verwacht: `photo-feest-2.webp` onder 200KB (was 636KB), de andere twee aanwezig.

- [ ] **Stap 5: Commit**

```bash
git add public/images/photo-petanque.webp public/images/grote-kuis.webp public/images/photo-feest-2.webp
git commit -m "feat: convert jpg assets to webp and re-optimize feest-2"
```

---

### Task 4: Update homepage view

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`

- [ ] **Stap 1: Vervang photo-feest-2.webp door photo-visitors-2.webp in openingsuren**

In `resources/views/activiteiten/index.blade.php`, zoek de openingsuren sectie (rond regel 184) en wijzig:

```blade
{{-- Van: --}}
<img src="{{ asset('images/photo-feest-2.webp') }}" alt="{{ __('pages.home_photo_feest_alt') }}"

{{-- Naar: --}}
<img src="{{ asset('images/photo-visitors-2.webp') }}" alt="{{ __('pages.home_photo_feest_alt') }}"
```

- [ ] **Stap 2: Draai de homepage test**

```bash
php artisan test --compact tests/Feature/ActivityControllerTest.php
```

Verwacht: PASS (of zoek de relevante homepage test op met `php artisan test --compact --filter=homepage`).

- [ ] **Stap 3: Commit**

```bash
git add resources/views/activiteiten/index.blade.php
git commit -m "fix: replace photo-feest-2 with photo-visitors-2 on homepage openingsuren"
```

---

### Task 5: Update activiteiten overzicht view

**Files:**
- Modify: `resources/views/activiteiten/overzicht.blade.php`

- [ ] **Stap 1: Vervang foto's in de `$themes` array (regels 22–60)**

De `$themes` array heeft 4 entries. Pas de `'photo'` waarden aan:

```php
// Themakaart 1 — Beweeg mee: groep-actief → petanque
'photo' => 'photo-petanque.webp',

// Themakaart 2 — Maak iets: visitors-2 → handwerk
'photo' => 'photo-handwerk.webp',
```

Themakaarten 3 (Praat & leer, `photo-samen.webp`) en 4 (Vier mee, `photo-party.webp`) blijven ongewijzigd.

- [ ] **Stap 2: Vervang bijzondere momenten foto's**

Zoek de drie foto's in de bijzondere momenten sectie en vervang:

```blade
{{-- Groot (spans 2 rijen) — was photo-feest-2.webp: --}}
<img src="{{ asset('images/photo-uitstap.webp') }}" alt=""

{{-- Midden boven — was photo-buiten-event.webp: --}}
<img src="{{ asset('images/photo-muzikanten.webp') }}" alt=""

{{-- Midden onder — was photo-cake.jpg: --}}
<img src="{{ asset('images/photo-verjaardag.webp') }}" alt=""
```

- [ ] **Stap 3: Draai de overzicht test**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Verwacht: PASS.

- [ ] **Stap 4: Commit**

```bash
git add resources/views/activiteiten/overzicht.blade.php
git commit -m "fix: update photo mapping on activiteiten overzicht page"
```

---

### Task 6: Update diensten view

**Files:**
- Modify: `resources/views/pages/diensten.blade.php`

- [ ] **Stap 1: Vervang vervoer foto**

Zoek `photo-vervoer.webp` in de foto strip sectie (rond regel 136) en vervang:

```blade
{{-- Van: --}}
<img src="{{ asset('images/photo-vervoer.webp') }}"
     alt="{{ app()->getLocale() === 'fr' ? 'Service de transport' : 'Vervoersdienst' }}"

{{-- Naar: --}}
<img src="{{ asset('images/photo-harmonie-bus.webp') }}"
     alt="{{ app()->getLocale() === 'fr' ? 'Service de transport' : 'Vervoersdienst' }}"
```

- [ ] **Stap 2: Vervang grote-kuis.jpg**

Zoek `grote-kuis.jpg` (rond regel 194) en vervang:

```blade
{{-- Van: --}}
<img src="{{ asset('images/grote-kuis.jpg') }}"

{{-- Naar: --}}
<img src="{{ asset('images/grote-kuis.webp') }}"
```

- [ ] **Stap 3: Draai de diensten test**

```bash
php artisan test --compact tests/Feature/DienstenPageTest.php
```

Verwacht: PASS.

- [ ] **Stap 4: Commit**

```bash
git add resources/views/pages/diensten.blade.php
git commit -m "fix: replace vervoer and grote-kuis photos on diensten page"
```

---

### Task 7: Update weekmenu view

**Files:**
- Modify: `resources/views/pages/weekmenu.blade.php`

- [ ] **Stap 1: Vervang de twee foto's**

Zoek in `resources/views/pages/weekmenu.blade.php` (rond regels 76–82):

```blade
{{-- Van: --}}
<img src="{{ asset('images/photo-chef-taart.webp') }}" ...>
...
<img src="{{ asset('images/photo-groep-tafel.webp') }}" ...>

{{-- Naar: --}}
<img src="{{ asset('images/photo-chef-taart-2.webp') }}" ...>
...
<img src="{{ asset('images/photo-restaurant-bord.webp') }}" ...>
```

`photo-feest-2.webp` (de derde foto) blijft — die is al hergeoptimaliseerd in Task 3.

- [ ] **Stap 2: Draai de weekmenu test**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Verwacht: PASS.

- [ ] **Stap 3: Commit**

```bash
git add resources/views/pages/weekmenu.blade.php
git commit -m "fix: update photo mapping on weekmenu page"
```

---

### Task 8: Draai image assets test — verwacht gedeeltelijk groen

**Files:** geen

- [ ] **Stap 1: Draai de image assets test**

```bash
php artisan test --compact tests/Feature/ImageAssetsTest.php
```

Verwacht: `test_all_referenced_images_exist` → **PASS**  
Verwacht: `test_deprecated_images_are_removed` → **FAIL** (bestanden bestaan nog)

---

### Task 9: Verwijder verouderde bestanden

**Files:**
- Delete: `public/images/photo-cake.jpg`
- Delete: `public/images/grote-kuis.jpg`
- Delete: `public/images/photo-chef-taart.webp`
- Delete: `public/images/photo-buiten-event.jpg`
- Delete: `public/images/photo-buiten-event.webp`
- Delete: `public/images/photo-petanque.jpg`

- [ ] **Stap 1: Verwijder de bestanden**

```bash
rm public/images/photo-cake.jpg \
   public/images/grote-kuis.jpg \
   public/images/photo-chef-taart.webp \
   public/images/photo-buiten-event.jpg \
   public/images/photo-buiten-event.webp \
   public/images/photo-petanque.jpg
```

- [ ] **Stap 2: Controleer dat ze weg zijn**

```bash
ls public/images/photo-cake.jpg public/images/grote-kuis.jpg public/images/photo-chef-taart.webp 2>&1
```

Verwacht: `No such file or directory` voor alle drie.

- [ ] **Stap 3: Draai volledige image assets test — beide groen**

```bash
php artisan test --compact tests/Feature/ImageAssetsTest.php
```

Verwacht: beide tests **PASS**.

- [ ] **Stap 4: Commit**

```bash
git add -u public/images/
git add tests/Feature/ImageAssetsTest.php
git commit -m "chore: remove deprecated image files and add image assets test"
```

---

### Task 10: Volledige test suite

- [ ] **Stap 1: Draai alle tests**

```bash
php artisan test --compact
```

Verwacht: alle tests groen. Als er failures zijn, controleer of een view nog een verwijdering naar een verwijderd bestand heeft.

- [ ] **Stap 2: Controleer de site visueel**

Open in browser:
- `https://deharmonie.test/` — foto strip + openingsuren + gebouw
- `https://deharmonie.test/activiteiten` — themakaarten + bijzondere momenten
- `https://deharmonie.test/diensten` — foto strip + grote kuis
- `https://deharmonie.test/restaurant-menu` — drie foto's

- [ ] **Stap 3: Pint formattering**

```bash
vendor/bin/pint --dirty --format agent
```

Verwacht: geen wijzigingen (er is geen PHP aangeraakt, enkel Blade).

---

## Opmerkingen

- `photo-gebouw.webp` heeft dezelfde bestandsnaam als het bestaande bestand — de view hoeft niet aangepast te worden, maar het bestand wordt wel overschreven in Task 2.
- `photo-feest-2.webp` blijft in gebruik op de weekmenu pagina maar is hergeoptimaliseerd van 636KB naar ~120KB.
- De Over ons foto's (`photo-gemeenschap.webp`, `photo-krijtbord.webp`, `photo-keukenteam.webp`, `photo-verjaardag-team.webp`) worden aangemaakt maar nog niet in de view geïntegreerd — de pagina is in opbouw. Ze zijn al mee in de assets test opgenomen zodra de pagina wordt uitgewerkt.
