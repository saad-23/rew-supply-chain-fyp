<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'lead_time_days'];

    public function products()
    {
        return $this->hasMany(Product::class); // Assuming we add supplier_id to products later or pivot
    }
}
