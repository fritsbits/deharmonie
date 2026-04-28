# De Harmonie

The bilingual (NL/FR) website for [De Harmonie](https://deharmonie.be), a community centre for seniors in Brussels' Noordwijk neighbourhood. Visitors browse upcoming activities, read the weekly menu, and register for events; staff manage everything through a Filament admin panel.

## Stack

- **Laravel 13** with **Livewire 3** for the public site
- **Filament 4** for the admin panel at `/admin`
- **Tailwind CSS v4** + **Vite** for the frontend
- **MySQL** for storage, **Spatie Media Library** for image uploads
- **PHPUnit** for the test suite

## Local setup

The project is designed for [Laravel Herd](https://herd.laravel.com) (macOS), which serves it at `https://deharmonie.test` over HTTPS.

```bash
git clone https://github.com/fritsbits/deharmonie.git
cd deharmonie

composer install
npm install
cp .env.example .env
php artisan key:generate

# Create a MySQL database called `harmonie`, then:
php artisan migrate --seed

npm run dev
```

Default DB connection assumes MySQL on `127.0.0.1:3306` with user `root` and no password (matches DBngin defaults). Adjust `.env` if your setup differs.

## Common commands

```bash
# Run the test suite
php artisan test

# Run a single test
php artisan test --filter=test_homepage_shows_published_activities

# Reset and reseed the database
php artisan migrate:fresh --seed

# Format PHP
vendor/bin/pint

# Build production assets
npm run build
```

## Architecture notes

- **Bilingual routing**: NL routes have no prefix, FR routes are prefixed with `/fr`. The `SetLocale` middleware (aliased as `locale`) takes the locale as a parameter — see `routes/web.php` and `bootstrap/app.php`.
- **Dutch table names**: `Activiteit` and `Deelnameverzoek` declare explicit `$table` properties because Laravel's auto-pluralisation breaks Dutch nouns.
- **Locale-aware accessors** on the `Activiteit` model (`getTitelAttribute`, `getBeschrijvingAttribute`, `getNoticeAttribute`) return the NL or FR field based on `app()->getLocale()`.
- **CMS is the source of truth**: seeders in `database/seeders/` are insert-only (`insertOrIgnore` / `firstOrCreate`) so re-running them never overwrites admin edits. See `CLAUDE.md` for the full content workflow.

## License

MIT — see [LICENSE](LICENSE).
