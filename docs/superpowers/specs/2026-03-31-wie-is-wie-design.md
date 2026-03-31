# Spec: Wie is Wie — database-driven teamledenbeher

**Datum:** 2026-03-31

## Doel

De "Wie is Wie"-pagina moet beheerd kunnen worden vanuit Filament, zonder dat teamleden of categorieën hardcoded in de Blade template staan. Wanneer iemand start, stopt of van functie verandert, past een beheerder dit aan in Filament.

## Scope

- Twee nieuwe database-tabellen: `team_categorieen` en `team_leden`
- Categorieën zijn vaste data (seeder), niet bewerkbaar in Filament
- Teamleden zijn volledig bewerkbaar via één Filament resource
- De Blade view laadt data dynamisch uit de database
- Governance-groepen (Bestuursorgaan, Wijkraad) worden als gewone categorieën behandeld

## Database

### `team_categorieen`

| kolom       | type         | opmerkingen                          |
|-------------|--------------|--------------------------------------|
| id          | bigint PK    |                                      |
| naam_nl     | string       | bijv. "Onthaal & Animatie"           |
| naam_fr     | string       | bijv. "Accueil & Animation"          |
| volgorde    | unsignedInt  | bepaalt de volgorde op de pagina     |
| timestamps  |              |                                      |

### `team_leden`

| kolom               | type         | opmerkingen                          |
|---------------------|--------------|--------------------------------------|
| id                  | bigint PK    |                                      |
| team_categorie_id   | FK → team_categorieen.id | cascade delete          |
| naam                | string       | volledige naam                       |
| titel_nl            | string null  | optionele functietitel in het NL     |
| titel_fr            | string null  | optionele functietitel in het FR     |
| volgorde            | unsignedInt  | volgorde binnen de categorie         |
| timestamps          |              |                                      |

## Modellen

### `TeamCategorie`

- `$table = 'team_categorieen'` (expliciete tabelnaam, Dutch pluralization)
- `hasMany(TeamLid::class)`
- Accessor `getNaamAttribute()` → geeft `naam_nl` of `naam_fr` op basis van `app()->getLocale()`

### `TeamLid`

- `$table = 'team_leden'`
- `belongsTo(TeamCategorie::class)`
- Accessor `getTitelAttribute()` → geeft `titel_nl` of `titel_fr`, of `null` als beide leeg

## Seeder

`TeamCategorieSeeder` seeded de volgende categorieën (in volgorde) met de bestaande namen en leden uit de huidige Blade template:

**Staf:**
1. Onthaal & Animatie / Accueil & Animation
2. Keuken – Chefs & Instructeurs / Cuisine – Chefs & Instructeurs
3. Zaal – Instructeur / Salle – Instructeur
4. Keuken- & Zaalassistenten / Assistants Cuisine & Salle
5. Transport & Onderhoud / Transport & Entretien
6. Poetsdienst / Service de nettoyage
7. Boekhouding & Administratie / Comptabilité & Administration
8. Coördinatie / Coordination

**Governance:**
9. Bestuursorgaan / Organe d'administration
10. Wijkraad / Conseil de quartier

Alle bestaande namen worden als `TeamLid` records aangemaakt. Titels starten allemaal op `null`.

## Filament Resource

**`TeamLidResource`**

- Navigatie-label: "Teamleden"
- Navigatie-icoon: `heroicon-o-user-group`
- Navigatie-groep: geen (staat los, net als Weekmenu)

### Tabel (index)

Kolommen: naam, titel (NL), categorie (naam NL), volgorde. Gesorteerd op categorie.volgorde + lid.volgorde.

### Formulier (create/edit)

- `naam` — TextInput, required
- `titel_nl` + `titel_fr` — TextInput naast elkaar, beide optioneel
- `team_categorie_id` — Select met alle categorieën (gesorteerd op volgorde), label = naam NL
- `volgorde` — TextInput (numeric), default 0

## View

`wie-is-wie.blade.php` laadt data via de `PageController::wieIsWie()` methode:

```php
$categorieen = TeamCategorie::with(['leden' => fn($q) => $q->orderBy('volgorde')])
    ->orderBy('volgorde')
    ->get();
```

De template itereert over categorieën en toont per lid: naam en optioneel de titel in de actieve taal. De huidige hardcoded PHP-array wordt volledig vervangen.

## Wat niet verandert

- De URL's (`/wie-is-wie`, `/fr/qui-est-qui`) blijven ongewijzigd
- De page-hero (eyebrow, heading, lead) blijft ongewijzigd
- De visuele opmaak van de pagina blijft ongewijzigd
- Categorieën zijn niet herbenoembaar of herordend vanuit Filament
