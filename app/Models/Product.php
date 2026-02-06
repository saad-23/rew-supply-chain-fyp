<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'current_stock',
        'price',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function demandForecasts()
    {
        return $this->hasMany(DemandForecast::class);
    }

    // Optional: If we relate products to suppliers in the future
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
