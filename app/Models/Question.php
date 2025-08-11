<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'image', 'school_class_id'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
