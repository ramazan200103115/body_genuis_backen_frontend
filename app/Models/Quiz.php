<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    public function creator()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function questions()
    {
        return $this->hasMany(related: Question::class, foreignKey: 'quiz_id', localKey: 'id');
    }

    public function participants()
    {
        return $this->hasMany(UserParticipant::class, 'quiz_id');
    }

}
