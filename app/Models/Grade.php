<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level'];

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }
}
