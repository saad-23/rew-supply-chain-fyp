<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'current_stock',
        'low_stock_threshold',
        'price',
        'category_id',
    ];

    /**
     * Returns true if stock is at or below the configured threshold.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->low_stock_threshold;
    }

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
