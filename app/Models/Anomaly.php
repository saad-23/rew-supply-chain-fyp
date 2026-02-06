<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'description', 'severity', 'is_resolved', 'detected_at'];

    protected $casts = [
        'detected_at' => 'datetime',
        'is_resolved' => 'boolean',
    ];
}
