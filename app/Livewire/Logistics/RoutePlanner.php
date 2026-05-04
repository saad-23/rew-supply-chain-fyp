<?php

namespace App\Livewire\Logistics;

use App\Models\Delivery;
use App\Services\GoogleMapsService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class RoutePlanner extends Component
{
    public string $date;
    public array  $optimizedRoutes = [];
    public bool   $showMap         = false;
    public array  $warehouse       = ['lat' => 31.5204, 'lng' => 74.3587, 'name' => 'Main Warehouse (Lahore)'];
    public ?string $routeError     = null;
    public ?string $routePolyline  = null;
    public array   $routeLegs      = [];

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function loadSampleData(): void
    {
        $locations = [
            ['lat' => 31.5820, 'lng' => 74.3294, 'addr' => 'Badami Bagh, Lahore'],
            ['lat' => 31.4815, 'lng' => 74.3030, 'addr' => 'Peco Road, Lahore'],
            ['lat' => 31.4697, 'lng' => 74.2728, 'addr' => 'Johar Town, Lahore'],
            ['lat' => 31.5497, 'lng' => 74.3436, 'addr' => 'Railway Station, Lahore'],
            ['lat' => 31.5246, 'lng' => 74.3429, 'addr' => 'Anarkali, Lahore'],
        ];

        foreach ($locations as $loc) {
            Delivery::create([
                'customer_name' => 'Sample Customer ' . rand(100, 999),
                'address'       => $loc['addr'],
                'latitude'      => $loc['lat'],
                'longitude'     => $loc['lng'],
                'delivery_date' => now(),
                'status'        => 'pending',
            ]);
        }

        session()->flash('message', 'Sample deliveries added for today.');
    }

    public function optimize(GoogleMapsService $maps): void
    {
        $this->routeError    = null;
        $this->routePolyline = null;
        $this->routeLegs     = [];

        $deliveries = Delivery::whereDate('delivery_date', $this->date)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($deliveries->isEmpty()) {
            $this->routeError = 'No deliveries with location data found for this date.';
            return;
        }

        $waypoints = $deliveries->map(fn($d) => [
            'lat'   => (float) $d->latitude,
            'lng'   => (float) $d->longitude,
            'id'    => $d->id,
            'label' => $d->customer_name,
        ])->values()->toArray();

        if ($maps->isConfigured()) {
            $result = $maps->optimizeRoute(
                $this->warehouse,
                $this->warehouse,   // round-trip back to warehouse
                $waypoints
            );

            if ($result['error']) {
                $this->routeError = $result['error'];
                // Fall back to original order
                $this->optimizedRoutes = $deliveries->toArray();
            } else {
                // Re-order deliveries according to Google's optimized waypoint order
                $ordered = [];
                foreach ($result['optimized_order'] as $idx) {
                    $ordered[] = $deliveries[$idx];
                }
                $this->optimizedRoutes = $ordered;
                $this->routePolyline   = $result['polyline'];
                $this->routeLegs       = $result['legs'];
            }
        } else {
            // No API key — use simple nearest-neighbour fallback
            $this->optimizedRoutes = $deliveries->toArray();
            $this->routeError = 'Google Maps API key not set — showing unoptimized order. Add GOOGLE_MAPS_API_KEY to .env.';
        }

        $this->showMap = true;
        $this->dispatch('routes-optimized', [
            'routes'   => $this->optimizedRoutes,
            'polyline' => $this->routePolyline,
            'warehouse' => $this->warehouse,
        ]);
    }

    public function render()
    {
        return view('livewire.logistics.route-planner', [
            'deliveries'  => Delivery::whereDate('delivery_date', $this->date)->get(),
            'mapsApiKey'  => app(GoogleMapsService::class)->getApiKey(),
            'mapsReady'   => app(GoogleMapsService::class)->isConfigured(),
        ]);
    }
}

