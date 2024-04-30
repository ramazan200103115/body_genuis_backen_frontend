<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationImages extends Model
{
    use HasFactory;

    protected $fillable = [
      'education_id',
      'url',
      'is_info'
    ];

    public function education()
    {
        $this->belongsTo(Education::class,'education_id','id');
    }
}
