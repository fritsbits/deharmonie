<?php

namespace App\Models;

use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActiviteitTemplate extends Model
{
    use HasFactory;

    protected $table = 'activiteit_templates';

    protected $fillable = [
        'titel_nl', 'titel_fr',
        'beschrijving_nl', 'beschrijving_fr',
        'notice_nl', 'notice_fr',
        'startuur', 'einduur',
        'locatie', 'prijs', 'max_deelnemers',
        'interesse', 'dag_van_de_week',
        'reeks_start', 'reeks_einde',
    ];

    protected $casts = [
        'interesse' => Interesse::class,
        'dag_van_de_week' => 'integer',
        'reeks_start' => 'date',
        'reeks_einde' => 'date',
        'prijs' => 'decimal:2',
    ];

    public function activiteiten(): HasMany
    {
        return $this->hasMany(Activiteit::class, 'template_id');
    }
}
