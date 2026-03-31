<?php

namespace App\Models;

use Database\Factories\TeamLidFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamLid extends Model
{
    /** @use HasFactory<TeamLidFactory> */
    use HasFactory;

    protected $table = 'team_leden';

    protected $fillable = ['team_categorie_id', 'naam', 'titel_nl', 'titel_fr', 'volgorde'];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(TeamCategorie::class, 'team_categorie_id');
    }

    public function getTitelAttribute(): ?string
    {
        $titel = app()->getLocale() === 'fr' ? $this->titel_fr : $this->titel_nl;

        return $titel ?: null;
    }
}
