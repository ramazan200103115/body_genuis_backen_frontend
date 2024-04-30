<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id'
    ];
    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    public function options() {
        return $this->hasMany(related: Option::class, foreignKey: 'question_id', localKey: 'id');
    }
}
