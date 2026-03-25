<?php

namespace App\Models;

use App\Enums\ActiviteitStatus;
use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Activiteit extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        $count = $this->deelnameverzoeken()
            ->whereIn('status', ['te_contacteren', 'afgehandeld'])
            ->count();

        return $count < $this->max_deelnemers;
    }

    public function getPrijsLabel(string $locale = 'nl'): string
    {
        if ($this->prijs === null || (float) $this->prijs === 0.0) {
            return $locale === 'fr' ? 'Gratuit' : 'Gratis';
        }

        return '€ '.number_format((float) $this->prijs, 2, ',', '.');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('afbeelding')->singleFile();
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
