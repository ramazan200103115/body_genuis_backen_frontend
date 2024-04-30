<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'urlAvatar',
        'name',
        'email',
        'age',
        'medcoins',
        'aboutMe',
        'password',
        'status'
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
    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'user_participants')->withPivot('score', 'participation_status');
    }

    public function participants()
    {
        return $this->hasMany(UserParticipant::class, 'user_id');
    }

    // Add a new method for passed quizzes
    public function passedQuizzes()
    {
        return $this->hasMany(UserParticipant::class, 'user_id')->where('score', '>', 50);
    }

}
