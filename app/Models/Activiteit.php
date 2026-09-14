<?php

namespace App\Models;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Interesse;
use App\Enums\Soort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Activiteit extends Model
{
    use HasFactory;

    protected $table = 'activiteiten';

    protected $fillable = [
        'slug', 'titel_nl', 'titel_fr',
        'beschrijving_nl', 'beschrijving_fr',
        'notice_nl', 'notice_fr',
        'datum', 'startuur', 'einduur',
        'locatie_nl', 'locatie_fr',
        'prijs', 'max_deelnemers',
        'status', 'interesse',
        'soort', 'categorie',
    ];

    protected $casts = [
        'datum' => 'date',
        'prijs' => 'decimal:2',
        'status' => ActiviteitStatus::class,
        'interesse' => Interesse::class,
        'soort' => Soort::class,
        'categorie' => Categorie::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (Activiteit $activiteit): void {
            if (blank($activiteit->slug)) {
                $activiteit->slug = static::generateUniqueSlug($activiteit->titel_nl, $activiteit->titel_fr);
            }
        });
    }

    /**
     * Build a unique, never-empty slug. Titles without sluggable characters
     * (e.g. emoji only) fall back to the French title, then to "activiteit".
     */
    public static function generateUniqueSlug(?string $titelNl, ?string $titelFr = null): string
    {
        $base = collect([$titelNl, $titelFr])
            ->map(fn (?string $title): string => Str::slug((string) $title))
            ->first(fn (string $slug): bool => $slug !== '', 'activiteit');
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    public function deelnameverzoeken(): HasMany
    {
        return $this->hasMany(Deelnameverzoek::class);
    }

    public function isBeschikbaar(): bool
    {
        if ($this->max_deelnemers === null) {
            return true;
        }

        return $this->deelnameverzoeken()->count() < $this->max_deelnemers;
    }

    public function getPrijsLabel(string $locale = 'nl'): string
    {
        if ($this->prijs === null || (float) $this->prijs === 0.0) {
            return $locale === 'fr' ? 'Gratuit' : 'Gratis';
        }

        return '€ '.number_format((float) $this->prijs, 2, ',', '.');
    }

    public function getTitelAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->titel_fr : $this->titel_nl;
    }

    public function getBeschrijvingAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->beschrijving_fr : $this->beschrijving_nl;
    }

    public function getNoticeAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->notice_fr : $this->notice_nl;
    }

    public function getLocatieAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->locatie_fr : $this->locatie_nl;
    }
}
