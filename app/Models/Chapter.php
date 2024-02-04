<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable=['title','url','serie_id'];

    public function serie():BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }
}
