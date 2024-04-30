<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
      'title', 'info', 'diseases'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'education_id');
    }
    public function images()
    {
        $this->hasMany(EducationImages::class,'education_id','id');
    }
}
