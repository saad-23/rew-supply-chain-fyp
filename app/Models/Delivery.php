<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 
        'address', 
        'latitude', 
        'longitude', 
        'priority', 
        'status', 
        'delivery_date'
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];
}
