<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grade_id', 'language'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
