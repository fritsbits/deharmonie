# Fotomapping & Foto-optimalisatie — Design Spec

**Datum:** 2026-03-31  
**Status:** Goedgekeurd

## Doel

Alle foto's op de juiste pagina en plek zetten, en tegelijk de bestandsgrootte en formaatproblemen oplossen. Twee deeltaken die samen worden uitgevoerd:

1. **Fotomapping** — nieuwe foto's toevoegen, verkeerde foto's vervangen, views aanpassen
2. **Foto-optimalisatie** — alles naar .webp converteren op correcte afmetingen, zware bestanden herexporteren, verouderde .jpg/.webp-rommel opruimen

---

## Bronnen

- **Bestaand:** `public/images/photo-*.webp` en `public/images/photo-*.jpg`
- **Nieuw van Facebook:** `~/Downloads/de-harmonie/fotos/*.jpg` (53 bestanden)
- **Van Webflow (pétanque):** al gekopieerd naar `public/images/photo-petanque.jpg`

---

## Definitieve fotomapping per pagina

### Homepage (`resources/views/activiteiten/index.blade.php`)

| Slot | Bestand | Status |
|------|---------|--------|
| Hero | `illustration-header.png` | Behouden |
| Foto strip col 1 | `photo-groep-tafel.webp` | Behouden |
| Foto strip col 2 | `photo-party.webp` | Behouden |
| Foto strip col 3 | `photo-groep-actief.webp` | Behouden |
| Openingsuren col 1 | `photo-thumbsup.webp` | Behouden |
| Openingsuren col 2 | `photo-samen.webp` | Behouden |
| Openingsuren col 3 | `photo-visitors-2.webp` | **Nieuw** — was `photo-feest-2.webp` |
| Gebouw | `photo-gebouw.webp` | **Vervangen** door `481072436_...jpg` |

### Activiteiten overzicht (`resources/views/activiteiten/overzicht.blade.php`)

| Slot | Bestand | Status |
|------|---------|--------|
| Themakaart: Beweeg mee | `photo-petanque.webp` | **Nieuw** — was `photo-groep-actief.webp` |
| Themakaart: Maak iets | `photo-handwerk.webp` | **Nieuw** — was `photo-visitors-2.webp` |
| Themakaart: Praat & leer | `photo-samen.webp` | Behouden |
| Themakaart: Vier mee | `photo-party.webp` | Behouden |
| Bijzondere momenten — groot | `photo-uitstap.webp` | **Nieuw** — was `photo-feest-2.webp` |
| Bijzondere momenten — midden boven | `photo-muzikanten.webp` | **Nieuw** — was `photo-buiten-event.webp` (verwijderd) |
| Bijzondere momenten — midden onder | `photo-verjaardag.webp` | **Nieuw** — was `photo-cake.jpg` |

### Diensten (`resources/views/pages/diensten.blade.php`)

| Slot | Bestand | Status |
|------|---------|--------|
| Foto strip: Vervoer | `photo-harmonie-bus.webp` | **Nieuw** — was `photo-vervoer.webp` |
| Foto strip: Onthaal | `photo-onthaal.webp` | Behouden |
| Foto strip: Restaurant | `photo-keuken-chefs.webp` | Behouden |
| Grote Kuis sectie | `grote-kuis.webp` | **Converteren** — was `grote-kuis.jpg` |

### Weekmenu (`resources/views/pages/weekmenu.blade.php`)

| Slot | Bestand | Status |
|------|---------|--------|
| Foto 1 | `photo-chef-taart-2.webp` | **Nieuw** — was `photo-chef-taart.webp` (600px, te laag res) |
| Foto 2 | `photo-restaurant-bord.webp` | **Nieuw** — was `photo-groep-tafel.webp` |
| Foto 3 | `photo-feest-2.webp` | Behouden, maar **heroptimaliseren** (636KB → ~120KB) |

### Over ons (`resources/views/pages/over-ons.blade.php`)

Pagina is momenteel "in opbouw". De foto's worden alvast klaargelegd maar de pagina-layout is nog niet uitgewerkt. Sectie 2 (net onder header) heeft **geen** rechtse afbeelding.

| Slot | Bestand | Status |
|------|---------|--------|
| Sfeerfoto 1 | `photo-gemeenschap.webp` | **Nieuw** — lachende man aan tafel |
| Sfeerfoto 2 | `photo-krijtbord.webp` | **Nieuw** — krijtbord met NL/FR programma |
| Het team 1 | `photo-keukenteam.webp` | **Nieuw** — volledig keukenteam (4 chefs) |
| Het team 2 | `photo-verjaardag-team.webp` | **Nieuw** — medewerker met verjaardagstaart |

---

## Nieuwe bestanden — bronnen en doelnamen

| Doelnaam | Bron | Locatie |
|----------|------|---------|
| `photo-gebouw.webp` | `481072436_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-petanque.webp` | `photo-petanque.jpg` | Al in `public/images/` |
| `photo-handwerk.webp` | `482028050_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-uitstap.webp` | `481665227_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-muzikanten.webp` | `483510180_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-verjaardag.webp` | `482218580_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-harmonie-bus.webp` | `484524442_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-chef-taart-2.webp` | `484750962_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-gemeenschap.webp` | `483963718_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-krijtbord.webp` | `484023936_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-keukenteam.webp` | `482345577_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `photo-verjaardag-team.webp` | `484935823_...jpg` | `~/Downloads/de-harmonie/fotos/` |
| `grote-kuis.webp` | `grote-kuis.jpg` | Al in `public/images/` |

---

## Foto-optimalisatie spec

### Conversie-instellingen (alle nieuwe + hergeconverteerde bestanden)

- **Formaat:** WebP
- **Max breedte:** 1200px (hoogte proportioneel schaald)
- **Kwaliteit:** 80%
- **Tool:** `cwebp` of `sips` (macOS)

### Specifieke aanpassingen bestaande bestanden

| Bestand | Probleem | Actie |
|---------|---------|-------|
| `photo-feest-2.webp` | 636KB — wordt 3× geladen, nu nog enkel weekmenu | Herexporteren op 1200px, 80% kwal. → ~120KB |
| `photo-cake.jpg` | .jpg formaat + gebruikt op overzicht | Vervangen door `photo-verjaardag.webp` |
| `grote-kuis.jpg` | .jpg formaat | Converteren naar `grote-kuis.webp` |
| `photo-chef-taart.webp` | 600×600px — te laag res | Vervangen door `photo-chef-taart-2.webp` |
| `photo-buiten-event.webp` | 525×700px — te laag res | Niet meer gebruiken |
| `photo-petanque.jpg` | .jpg, 800×534px | Converteren naar `photo-petanque.webp` |

### Op te ruimen na migratie (verouderde bestanden — niet verwijderen vóór views geüpdated zijn)

Volgende bestanden worden na de migratie niet meer gebruikt en kunnen worden verwijderd:
- `photo-feest-2.jpg` (origineel .jpg naast .webp)
- `photo-cake.jpg`
- `grote-kuis.jpg`
- `photo-chef-taart.webp` (lage res versie)
- `photo-buiten-event.jpg` + `photo-buiten-event.webp`
- `photo-petanque.jpg` (vervangen door .webp)
- Alle `.jpg` originals waarvan een `.webp` versie bestaat en die nergens meer gebruikt worden

---

## Views aan te passen

### `resources/views/activiteiten/index.blade.php`
- Openingsuren col 3: `photo-feest-2.webp` → `photo-visitors-2.webp`
- Gebouw: `photo-gebouw.webp` (bestandsnaam hetzelfde, inhoud vernieuwd)

### `resources/views/activiteiten/overzicht.blade.php`
- Themakaart "Beweeg mee" (`$themes` array): `photo-groep-actief.webp` → `photo-petanque.webp`
- Themakaart "Maak iets" (`$themes` array): `photo-visitors-2.webp` → `photo-handwerk.webp`
- Bijzondere momenten groot: `photo-feest-2.webp` → `photo-uitstap.webp`
- Bijzondere momenten midden boven: `photo-buiten-event.webp` → `photo-muzikanten.webp`
- Bijzondere momenten midden onder: `photo-cake.jpg` → `photo-verjaardag.webp`

### `resources/views/pages/diensten.blade.php`
- Foto strip vervoer: `photo-vervoer.webp` → `photo-harmonie-bus.webp`
- Grote Kuis sectie: `grote-kuis.jpg` → `grote-kuis.webp`

### `resources/views/pages/weekmenu.blade.php`
- Foto 1: `photo-chef-taart.webp` → `photo-chef-taart-2.webp`
- Foto 2: `photo-groep-tafel.webp` → `photo-restaurant-bord.webp`
- (Foto 3 `photo-feest-2.webp` blijft, maar wordt hergeoptimaliseerd)

### `resources/views/pages/over-ons.blade.php`
- Pagina is in opbouw — foto's klaarzetten als variabelen/assets, layout nog niet uitgewerkt

---

## Wat NIET verandert

- `photo-groep-tafel.webp` — blijft op homepage strip (col 1); op weekmenu wordt het vervangen door `photo-restaurant-bord.webp`
- `photo-party.webp`, `photo-samen.webp`, `photo-thumbsup.webp` — ongewijzigd
- `photo-onthaal.webp`, `photo-keuken-chefs.webp` — ongewijzigd
- Alle logo's, iconen en interesses-afbeeldingen — niet aangeraakt
- Activiteit-detail foto's (via Spatie Media Library) — buiten scope
