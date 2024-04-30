<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionAnswer extends Model
{
    use HasFactory;

    public function questions() {
        return $this->belongsTo(related: Question::class, foreignKey: 'question_id', ownerKey: 'id');
    }
}
