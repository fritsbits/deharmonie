<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekMenuDag extends Model
{
    use HasFactory;

    protected $table = 'weekmenu_dagen';

    protected $fillable = [
        'date',
        'closed',
        'special_event',
        'price',
        'main_nl',
        'main_fr',
        'event_label_nl',
        'event_label_fr',
        'courses',
    ];

    protected $casts = [
        'date' => 'date',
        'closed' => 'boolean',
        'special_event' => 'boolean',
        'courses' => 'array',
    ];

    public function getMainAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->main_fr : $this->main_nl;
    }

    public function getEventLabelAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->event_label_fr : $this->event_label_nl;
    }

    public function getCoursesForLocaleAttribute(): array
    {
        if (empty($this->courses)) {
            return [];
        }
        $locale = app()->getLocale();

        return array_column($this->courses, $locale);
    }

    public function getTypeAttribute(): string
    {
        if ($this->closed) {
            return 'Gesloten';
        }
        if ($this->special_event) {
            return 'Speciaal';
        }

        return 'Normaal';
    }
}
