<?php

namespace App\Models;

use App\Enums\ActiviteitStatus;
use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'locatie', 'prijs', 'max_deelnemers', 'status', 'interesse',
        'template_id',
    ];

    protected $casts = [
        'datum' => 'date',
        'prijs' => 'decimal:2',
        'status' => ActiviteitStatus::class,
        'interesse' => Interesse::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Activiteit $activiteit): void {
            if (empty($activiteit->slug)) {
                $activiteit->slug = static::generateUniqueSlug($activiteit->titel_nl);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ActiviteitTemplate::class, 'template_id');
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

    public function getInteresseThumbnailUrlAttribute(): ?string
    {
        return $this->interesse?->thumbnailUrl();
    }
}
