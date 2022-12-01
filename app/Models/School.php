<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'lat', 'lng', 'distance', 'clock_in', 'clock_out', 'image'
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class, 'school_id');
    }
}
