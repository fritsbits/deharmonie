<?php

namespace App\Models;

use Database\Factories\OverOnsContentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OverOnsContent extends Model implements HasMedia
{
    /** @use HasFactory<OverOnsContentFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $table = 'over_ons_content';

    protected $fillable = [
        'jaarverslag_jaar',
        'impact_1_aantal', 'impact_1_omschrijving_nl', 'impact_1_omschrijving_fr',
        'impact_2_aantal', 'impact_2_omschrijving_nl', 'impact_2_omschrijving_fr',
        'impact_3_aantal', 'impact_3_omschrijving_nl', 'impact_3_omschrijving_fr',
    ];

    protected $casts = [
        'jaarverslag_jaar' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('jaarverslag')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'impact_1_aantal' => '0',
            'impact_1_omschrijving_nl' => '',
            'impact_1_omschrijving_fr' => '',
            'impact_2_aantal' => '0',
            'impact_2_omschrijving_nl' => '',
            'impact_2_omschrijving_fr' => '',
            'impact_3_aantal' => '0',
            'impact_3_omschrijving_nl' => '',
            'impact_3_omschrijving_fr' => '',
        ]);
    }

    public function impactOmschrijving(int $stat): string
    {
        $locale = app()->getLocale();
        $key = "impact_{$stat}_omschrijving_{$locale}";

        return (string) ($this->{$key} ?? '');
    }

    public function getJaarverslagUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('jaarverslag');

        return $url !== '' ? $url : null;
    }

    public function getJaarverslagSize(): ?string
    {
        $bytes = $this->getFirstMedia('jaarverslag')?->size;

        return $bytes ? Number::fileSize($bytes, precision: 1) : null;
    }
}
