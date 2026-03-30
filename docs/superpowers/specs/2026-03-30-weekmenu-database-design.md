# Week Menu Database Design

> **For agentic workers:** Use `superpowers:subagent-driven-development` or `superpowers:executing-plans` to implement this spec.

**Goal:** Replace the `weekmenu.json` file with a database-backed model and a Filament admin resource for managing menu entries.

---

## Data Model

### Table: `weekmenu_dagen`

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| `id` | bigint | no | — | PK |
| `date` | date | no | — | unique |
| `closed` | boolean | no | false | |
| `special_event` | boolean | no | false | |
| `price` | smallint | yes | null | null when closed |
| `main_nl` | string | yes | null | |
| `main_fr` | string | yes | null | |
| `event_label_nl` | string | yes | null | special event name |
| `event_label_fr` | string | yes | null | |
| `courses` | json | yes | null | `[{"nl":"...","fr":"..."}]` — one object per course |
| `created_at` | timestamp | — | — | |
| `updated_at` | timestamp | — | — | |

`date` has a unique index. `closed` and `special_event` are mutually exclusive by convention (a closed day is never also a special event).

### Model: `WeekMenuDag`

- Table: `weekmenu_dagen` (explicit `$table` — same reason as `Activiteit`)
- Casts: `date` → `date`, `closed` → `boolean`, `special_event` → `boolean`, `courses` → `array`
- Locale-aware accessors (follow `Activiteit` pattern, return `_nl` or `_fr` based on `app()->getLocale()`):
  - `getMainAttribute()` → `main_nl` or `main_fr`
  - `getEventLabelAttribute()` → `event_label_nl` or `event_label_fr`
- `getCoursesForLocaleAttribute()` — plucks `nl` or `fr` from each course object, returns a flat array of strings

### Display logic in views

| Day type | Soup line | Main content |
|---|---|---|
| Normal | `__('weekmenu.soup_default')` (static) | `$day->main` |
| Special event | none | `$day->event_label` + `$day->coursesForLocale` list |
| Closed | none | `__('weekmenu.closed')` |

### Lang changes

Add to `lang/nl/weekmenu.php` and `lang/fr/weekmenu.php`:
- `'soup_default'` → `'Soep van de dag'` (NL) / `'Potage du jour'` (FR)

---

## Filament Admin

### Resource: `WeekMenuDagResource`

**Table view** — sorted ascending by `date`:
- `date` column (formatted `d/m/Y`, sortable)
- Type badge — derived from `closed`/`special_event`: `Gesloten` (gray) / `Speciaal` (orange) / `Normaal` (green)
- `price` column (formatted `€ X`, blank when null)
- `main_nl` column (truncated, label "Gerecht (NL)")

Filters:
- `Deze week` / `Volgende week` / `Alles` — scopes by date range to keep the list usable when a month is pre-entered

**Form** — fields shown/hidden conditionally:

```
date            DatePicker   required, unique
closed          Toggle       default false
special_event   Toggle       hidden when closed=true; default false

-- when closed=false, special_event=false --
main_nl         TextInput    required
main_fr         TextInput    required
price           TextInput    numeric, required

-- when special_event=true --
event_label_nl  TextInput    required
event_label_fr  TextInput    required
price           TextInput    numeric, required
courses         Repeater     nullable
  course_nl     TextInput    required per row
  course_fr     TextInput    required per row
```

Soup is not on the form — it is always a static default.

Filament 4 API: `Filament\Schemas\Schema` (not `Filament\Forms\Form`). Use `->hidden(fn (Get $get) => ...)` for conditional visibility.

---

## Consumer Code Changes

### `app/Livewire/WeekMenu.php`

Replace JSON file read with:
```php
WeekMenuDag::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
    ->orderBy('date')
    ->get();
```

The `days()` computed property returns a `Collection` of `WeekMenuDag` models.

### Blade templates

Both `resources/views/livewire/week-menu.blade.php` and `resources/views/pages/weekmenu-print.blade.php`:

| Old (array) | New (model) |
|---|---|
| `$day['closed']` | `$day->closed` |
| `$day['special_event']` | `$day->special_event` |
| `$day['price']` | `$day->price` |
| `$day[$locale]['main']` | `$day->main` |
| `$day[$locale]['event_label']` | `$day->event_label` |
| `$day[$locale]['courses']` | `$day->coursesForLocale` |
| soup (hardcoded "Soep van de dag") | `__('weekmenu.soup_default')` |

### `app/Http/Controllers/PageController.php`

`weekmenuPrint` method: replace JSON file read (`file_get_contents` + `json_decode`) and the `$data` variable with `WeekMenuDag::whereBetween(...)->orderBy('date')->get()`. Keep `$locale` — still needed for Carbon `isoFormat()` date formatting. Pass the collection as `$days` to the view (same variable name).

---

## Data Migration

### `WeekMenuDagSeeder`

- Reads `resources/data/weekmenu.json`
- For each entry: `WeekMenuDag::updateOrCreate(['date' => $day['date']], [...])`
- Maps JSON fields to new columns (drops `soup_*`, `closed_label_*`)
- Safe to re-run

### `DatabaseSeeder`

Call `WeekMenuDagSeeder` from `DatabaseSeeder::run()`.

---

## Tests

### `WeekMenuDagFactory`

States:
- Default: normal open day, `main_nl` / `main_fr` filled, price set
- `closed()`: `closed=true`, price null, main null
- `specialEvent()`: `special_event=true`, `event_label_nl/fr` filled, courses JSON array, price set

### Test changes

- `tests/Feature/WeekMenuTest.php` — replace `Carbon::setTestNow` + JSON-dependent assertions with factory-created `WeekMenuDag` rows
- `tests/Feature/WeekMenuPrintTest.php` — same: use factory rows instead of relying on JSON data for date `2026-03-23`
- All existing assertions (seeing meal names, closed label, print link) remain — only the data source changes
