<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deelnameverzoek extends Model
{
    use HasFactory;

    protected $table = 'deelnameverzoeken';

    protected $fillable = [
        'activiteit_id', 'naam', 'email', 'telefoon', 'bericht', 'status',
    ];

    public function activiteit(): BelongsTo
    {
        return $this->belongsTo(Activiteit::class);
    }
}
