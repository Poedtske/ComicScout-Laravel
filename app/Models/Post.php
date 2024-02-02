<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\carbon;

class Post extends Model
{
    use HasFactory;

    protected $fillable=['title','description','cover','date','time',];

    public function getTime()
    {
        // Assuming birthday_time is the time column
        $Time = $this->attributes['time'];

        // Parse the time as Carbon instance
        $carbonTime = Carbon::parse($Time);

        // Format the time as hh:mm
        return $carbonTime->format('H:i');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
