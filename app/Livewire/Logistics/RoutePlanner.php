<?php

namespace App\Livewire\Logistics;

use App\Models\Delivery;
use App\Services\RouteOptimizationService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class RoutePlanner extends Component
{
    public $date;
    public $optimizedRoutes = [];
    public $showMap = false;
    public $warehouse = ['lat' => 31.5204, 'lng' => 74.3587, 'name' => 'Main Warehouse (Lahore)'];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        // Ensure dummy data exists for demo
        if (Delivery::count() == 0) {
            $this->seedDeliveries();
        }
    }

    public function seedDeliveries()
    {
        // Add some dummy deliveries around Lahore, Pakistan
        $locations = [
            ['lat' => 31.5204, 'lng' => 74.3587, 'addr' => 'Mughalpura, Lahore'],
            ['lat' => 31.5820, 'lng' => 74.3294, 'addr' => 'Badami Bagh, Lahore'],
            ['lat' => 31.4815, 'lng' => 74.3030, 'addr' => 'Peco Road, Lahore'],
            ['lat' => 31.4697, 'lng' => 74.2728, 'addr' => 'Johar Town, Lahore'],
            ['lat' => 31.5497, 'lng' => 74.3436, 'addr' => 'Railway Station, Lahore']
        ];

        foreach ($locations as $loc) {
            Delivery::create([
                'customer_name' => 'Customer ' . rand(100, 999),
                'address' => $loc['addr'],
                'latitude' => $loc['lat'],
                'longitude' => $loc['lng'],
                'delivery_date' => now(), // today
                'status' => 'pending'
            ]);
        }
    }

    public function optimize(RouteOptimizationService $service)
    {
        $this->optimizedRoutes = $service->optimizeRoutes($this->date);
        $this->showMap = true;
        $this->dispatch('routes-optimized', data: $this->optimizedRoutes);
    }

    public function render()
    {
        return view('livewire.logistics.route-planner', [
            'deliveries' => Delivery::whereDate('delivery_date', $this->date)->get()
        ]);
    }
}
