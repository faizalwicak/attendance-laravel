<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'is_leave'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leave()
    {
        return $this->hasOne(Leave::class, 'record_id');
    }

    public function attend()
    {
        return $this->hasOne(Attend::class, 'record_id');
    }
}
