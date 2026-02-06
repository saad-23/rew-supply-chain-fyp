<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'forecast_date', 
        'predicted_quantity', 
        'model_used', 
        'confidence_score'
    ];

    protected $casts = [
        'forecast_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
