<?php

namespace App\Livewire\Operations;

use App\Models\Delivery;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.admin')]
class CreateDelivery extends Component
{
    public $customer_name;
    public $address;
    public $latitude;
    public $longitude;
    public $resolved_address;
    public $delivery_date;
    public $priority = 1;
    public $suggestions = [];

    public function mount()
    {
        $this->delivery_date = now()->format('Y-m-d');
    }

    public function updatedAddress()
    {
        // Don't search if address is cleared or too short
        if (strlen($this->address) < 3) {
            $this->suggestions = [];
            return;
        }

        // Use Photon API for smarter suggestions
        try {
             $response = Http::get('https://photon.komoot.io/api/', [
                'q' => $this->address,
                'limit' => 5,
                'lat' => 31.5204, // Location Bias: Lahore
                'lon' => 74.3587,
                'lang' => 'en'
            ]);

            if ($response->successful()) {
                $features = $response->json()['features'] ?? [];
                
                $this->suggestions = array_map(function($f) {
                    $props = $f['properties'];
                    // Build a cleaner label
                    $parts = array_filter([
                        $props['name'] ?? null,
                        $props['street'] ?? null,
                        $props['district'] ?? null,
                        $props['city'] ?? null,
                        $props['state'] ?? null,
                        $props['country'] ?? null
                    ]);
                    
                    return [
                        'display_name' => implode(', ', $parts),
                        'lat' => $f['geometry']['coordinates'][1],
                        'lon' => $f['geometry']['coordinates'][0],
                        'type' => $props['osm_value'] ?? 'location'
                    ];
                }, $features);
            }
        } catch (\Exception $e) {
            $this->suggestions = [];
        }
    }

    public function selectSuggestion($index)
    {
        if (isset($this->suggestions[$index])) {
            $selected = $this->suggestions[$index];
            $this->address = $selected['display_name']; // Update input text
            $this->latitude = $selected['lat'];
            $this->longitude = $selected['lon'];
            $this->resolved_address = $selected['display_name'];
            $this->suggestions = []; // Close dropdown
        }
    }

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'delivery_date' => 'required|date',
        'priority' => 'required|in:1,2',
    ];

    // Geocode address using Photon (Komoot) for better fuzzy search
    public function geocodeAddress()
    {
        if (empty($this->address)) return;

        $this->resetErrorBag('address');
        $this->resolved_address = null;

        try {
            // Photon API search
            $response = Http::get('https://photon.komoot.io/api/', [
                'q' => $this->address,
                'limit' => 1,
                'lat' => 31.5204, // Bias toward Lahore
                'lon' => 74.3587
            ]);

            if ($response->successful() && !empty($response->json()['features'])) {
                $feature = $response->json()['features'][0];
                $props = $feature['properties'];
                
                $this->latitude = $feature['geometry']['coordinates'][1]; // Lat is index 1
                $this->longitude = $feature['geometry']['coordinates'][0]; // Lon is index 0
                
                // Construct a nice display name
                $parts = array_filter([
                    $props['name'] ?? null,
                    $props['street'] ?? null,
                    $props['city'] ?? null,
                    $props['state'] ?? null,
                    $props['country'] ?? null
                ]);
                $this->resolved_address = implode(', ', $parts);
                
                // Update input if it was partial
                $this->address = $this->resolved_address; 

            } else {
                $this->addError('address', 'Could not find coordinates. Please try a different spelling.');
            }
        } catch (\Exception $e) {
            $this->addError('address', 'Geocoding service unavailable.');
        }
    }

    public function save()
    {
        $this->validate();

        Delivery::create([
            'customer_name' => $this->customer_name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => 'pending',
            'delivery_date' => $this->delivery_date,
            'priority' => $this->priority
        ]);

        session()->flash('message', 'Delivery scheduled successfully via Route Optimizer.');
        $this->reset(['customer_name', 'address', 'latitude', 'longitude']);
    }

    public function render()
    {
        return view('livewire.operations.create-delivery', [
            'recentDeliveries' => Delivery::latest()->take(5)->get()
        ]);
    }
}
