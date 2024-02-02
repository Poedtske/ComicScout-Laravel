<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InstrumentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function instrument():BelongsTo
    {
        return $this->belongsTo(Instrument::class);
    }
}
