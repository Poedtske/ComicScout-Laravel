<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'avatar',
        'aboutme',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posts():HasMany
    {
        return $this->hasMany(Post::class);
    }
    public function getBirthday()
    {
        // Assuming birthday is the date column
        $birthday = $this->attributes['birthday'];

        // Extract month and day
        $month = date('m', strtotime($birthday));
        $day = date('d', strtotime($birthday));

        // Format the birthday as mm/dd
        return sprintf('%02d/%02d', $month, $day);
    }

    public function categories():HasMany
    {
        return $this->hasMany(Category::class);
    }
    public function questions():HasMany
    {
        return $this->hasMany(Question::class);
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function instruments():BelongsToMany
    {
        return $this->belongsToMany(Instrument::class);
    }
}
