<?php

namespace App\Livewire\Operations;

use App\Models\Delivery;
use App\Models\Product;
use App\Models\Setting;
use App\Services\GoogleMapsService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class CreateDelivery extends Component
{
    // ── Destination (saved to DB) ──────────────────────────────
    public string  $address          = '';
    public ?float  $latitude         = null;
    public ?float  $longitude        = null;
    public ?string $resolved_address = null;

    // ── Origin / warehouse (UI only, NOT saved to DB) ──────────
    public string $origin_address = '';
    public float  $origin_lat     = 31.5204;
    public float  $origin_lng     = 74.3587;

    // ── Form fields ────────────────────────────────────────────
    public ?string $customer_name = null;
    public string  $delivery_date = '';
    public int     $priority      = 1;
    public ?int    $product_id    = null;
    public int     $quantity      = 1;
    public ?string $notes         = null;

    public function mount(): void
    {
        $this->delivery_date = now()->format('Y-m-d');

        // Pre-fill warehouse address from settings
        $factory = Setting::where('key', 'factory_address')->value('value');
        $this->origin_address = $factory ?: 'Main Warehouse, Lahore, Pakistan';

        // Server-side geocode so the map centres on warehouse on load
        try {
            $maps = app(GoogleMapsService::class);
            if ($maps->isConfigured()) {
                $geo = $maps->geocode($this->origin_address);
                if ($geo) {
                    $this->origin_lat     = $geo['lat'];
                    $this->origin_lng     = $geo['lng'];
                    $this->origin_address = $geo['formatted_address'];
                }
            }
        } catch (\Throwable) {
            // Fallback coords already set above — silently continue
        }
    }

    /** Called from JS when user picks origin from Places autocomplete */
    public function setOriginCoords(float $lat, float $lng, string $address): void
    {
        $this->origin_lat     = $lat;
        $this->origin_lng     = $lng;
        $this->origin_address = $address;
    }

    /** Called from JS when user picks destination from Places autocomplete */
    public function setDestinationCoords(float $lat, float $lng, string $address): void
    {
        $this->latitude         = $lat;
        $this->longitude        = $lng;
        $this->address          = $address;
        $this->resolved_address = $address;
    }

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'address'       => 'required|string|max:500',
        'latitude'      => 'required|numeric|between:-90,90',
        'longitude'     => 'required|numeric|between:-180,180',
        'delivery_date' => 'required|date',
        'priority'      => 'required|in:1,2',
        'product_id'    => 'required|exists:products,id',
        'quantity'      => 'required|integer|min:1',
        'notes'         => 'nullable|string|max:500',
    ];

    protected $messages = [
        'latitude.required'  => 'Delivery address dropdown se select karen (map suggestion).',
        'longitude.required' => 'Delivery address dropdown se select karen (map suggestion).',
    ];

    public function save(): void
    {
        $this->validate();

        Delivery::create([
            'customer_name' => $this->customer_name,
            'address'       => $this->address,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'status'        => 'pending',
            'delivery_date' => $this->delivery_date,
            'priority'      => $this->priority,
            'product_id'    => $this->product_id,
            'quantity'      => $this->quantity,
            'notes'         => $this->notes,
        ]);

        session()->flash('message', 'Delivery scheduled successfully!');
        $this->reset(['customer_name', 'address', 'latitude', 'longitude',
                      'product_id', 'notes', 'resolved_address']);
        $this->delivery_date = now()->format('Y-m-d');
        $this->quantity      = 1;
        $this->priority      = 1;
    }

    public function render()
    {
        $maps = app(GoogleMapsService::class);
        return view('livewire.operations.create-delivery', [
            'recentDeliveries' => Delivery::with('product')->latest()->take(8)->get(),
            'products'         => Product::orderBy('name')->get(),
            'mapsApiKey'       => $maps->getApiKey(),
            'mapsReady'        => $maps->isConfigured(),
        ]);
    }
}
