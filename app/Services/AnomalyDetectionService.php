<?php

namespace App\Services;

use App\Models\Anomaly;
use App\Models\Delivery;
use App\Models\Sale;
use Carbon\Carbon;

class AnomalyDetectionService
{
    public function scanForAnomalies()
    {
        $anomaliesFound = 0;

        // 1. Detect Potential Delivery Delays
        $delayedDeliveries = Delivery::where('status', 'pending')
            ->whereDate('delivery_date', '<', Carbon::today())
            ->get();

        foreach ($delayedDeliveries as $delivery) {
            $exists = Anomaly::where('type', 'delivery_delay')
                ->where('description', 'like', "%#{$delivery->id}%")
                ->exists();
            
            if (!$exists) {
                Anomaly::create([
                    'type' => 'delivery_delay',
                    'description' => "Delivery #{$delivery->id} to {$delivery->customer_name} is overdue. Expected: {$delivery->delivery_date->toDateString()}",
                    'severity' => 'high',
                    'is_resolved' => false
                ]);
                $anomaliesFound++;
            }
        }

        // 2. Detect Sales Spikes (Simple Z-Score simulation or threshold)
        // Let's say any sale > 2x average quantity of that product is an outlier
        $sales = Sale::where('created_at', '>=', Carbon::now()->subDay())->get(); // Check last 24h
        
        foreach ($sales as $sale) {
            $avgQty = Sale::where('product_id', $sale->product_id)->avg('quantity');
            
            if ($avgQty > 0 && $sale->quantity > ($avgQty * 3)) { // 3x average
                 $exists = Anomaly::where('type', 'demand_surge')
                    ->where('description', 'like', "%Sale #{$sale->id}%")
                    ->exists();

                 if (!$exists) {
                    Anomaly::create([
                        'type' => 'demand_surge',
                        'description' => "Unusual large sale #{$sale->id} detected. Qty: {$sale->quantity} (Avg: " . number_format($avgQty, 1) . ")",
                        'severity' => 'medium',
                        'is_resolved' => false
                    ]);
                    $anomaliesFound++;
                 }
            }
        }

        return $anomaliesFound;
    }

    public function resolve(Anomaly $anomaly)
    {
        $anomaly->update(['is_resolved' => true]);
    }
}
