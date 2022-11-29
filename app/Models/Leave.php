<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;


    protected $fillable = [
        'record_id',
        'type',
        'description',
        'leave_status',
    ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
