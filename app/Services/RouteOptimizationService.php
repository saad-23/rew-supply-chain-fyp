<?php

namespace App\Services;

use App\Models\Delivery;
use Illuminate\Support\Collection;

class RouteOptimizationService
{
    // Warehouse: Lahore, Pakistan (approx center/Mughalpura)
    private $warehouseLocation = ['lat' => 31.5204, 'lng' => 74.3587];

    public function optimizeRoutes($date)
    {
        $deliveries = Delivery::whereDate('delivery_date', $date)
            ->where('status', 'pending')
            ->get();

        if ($deliveries->isEmpty()) {
            return collect([]);
        }

        // Simple Nearest Neighbor Algorithm
        $optimizedRoute = new Collection();
        $currentLocation = $this->warehouseLocation;
        $remainingDeliveries = $deliveries->keyBy('id');

        while ($remainingDeliveries->isNotEmpty()) {
            $nearestId = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($remainingDeliveries as $id => $delivery) {
                // Skip if no coords (simulate geocoding needed)
                if (!$delivery->latitude || !$delivery->longitude) continue;

                $distance = $this->calculateDistance(
                    $currentLocation['lat'], 
                    $currentLocation['lng'], 
                    $delivery->latitude, 
                    $delivery->longitude
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestId = $id;
                }
            }

            if ($nearestId) {
                $nearest = $remainingDeliveries[$nearestId];
                $optimizedRoute->push($nearest);
                $currentLocation = ['lat' => $nearest->latitude, 'lng' => $nearest->longitude];
                $remainingDeliveries->forget($nearestId);
            } else {
                // If remaining items have no coords, just append them at the end
                foreach ($remainingDeliveries as $remaining) {
                    $optimizedRoute->push($remaining);
                }
                break;
            }
        }

        return $optimizedRoute;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine formula
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
