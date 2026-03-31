<?php

namespace App\Models;

use Database\Factories\TeamCategorieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamCategorie extends Model
{
    /** @use HasFactory<TeamCategorieFactory> */
    use HasFactory;

    protected $table = 'team_categorieen';

    protected $fillable = ['naam_nl', 'naam_fr', 'volgorde'];

    public function leden(): HasMany
    {
        return $this->hasMany(TeamLid::class);
    }

    public function getNaamAttribute(): string
    {
        return app()->getLocale() === 'fr' ? $this->naam_fr : $this->naam_nl;
    }
}
