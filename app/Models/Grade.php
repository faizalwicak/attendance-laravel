<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'grade', 'school_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'grade_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'grade_id');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'admin_accesses'); // assuming user_id and task_id as fk
    }
}
