<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Serie extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    // protected $primaryKey=['id','title'];

    public function scanlator():BelongsTo
    {
        return $this->belongsTo(Scanlator::class);
    }

    public function chapters():HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function genres():BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }


    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function relatedSeries()
    {
        return $this->belongsToMany(Serie::class, 'serie_serie', 'parent_serie_id', 'child_serie_id');
    }
}
