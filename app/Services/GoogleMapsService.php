<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    private string $apiKey;
    private string $directionsBaseUrl = 'https://maps.googleapis.com/maps/api/directions/json';
    private string $distanceMatrixBaseUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';
    private string $geocodeBaseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key', '');
    }

    /**
     * Returns true only if an API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'YOUR_GOOGLE_MAPS_API_KEY_HERE';
    }

    /**
     * Get optimized waypoint order and encoded polyline from Google Directions API.
     *
     * @param  array  $origin       ['lat' => float, 'lng' => float]
     * @param  array  $destination  ['lat' => float, 'lng' => float]  (can be same as origin for round-trip)
     * @param  array  $waypoints    [['lat' => float, 'lng' => float, 'id' => int, 'label' => string], ...]
     * @return array  ['optimized_order' => int[], 'polyline' => string, 'legs' => array, 'error' => string|null]
     */
    public function optimizeRoute(array $origin, array $destination, array $waypoints): array
    {
        if (!$this->isConfigured()) {
            return ['optimized_order' => array_keys($waypoints), 'polyline' => null, 'legs' => [], 'error' => 'Google Maps API key not configured.'];
        }

        if (empty($waypoints)) {
            return ['optimized_order' => [], 'polyline' => null, 'legs' => [], 'error' => null];
        }

        // Build waypoints string with "optimize:true" prefix
        $waypointStrings = array_map(
            fn($wp) => "{$wp['lat']},{$wp['lng']}",
            $waypoints
        );

        $params = [
            'origin'            => "{$origin['lat']},{$origin['lng']}",
            'destination'       => "{$destination['lat']},{$destination['lng']}",
            'waypoints'         => 'optimize:true|' . implode('|', $waypointStrings),
            'mode'              => 'driving',
            'key'               => $this->apiKey,
        ];

        try {
            $response = Http::timeout(15)->get($this->directionsBaseUrl, $params);

            if (!$response->successful()) {
                return ['optimized_order' => array_keys($waypoints), 'polyline' => null, 'legs' => [], 'error' => 'Directions API HTTP error: ' . $response->status()];
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                $msg = match($data['status']) {
                    'ZERO_RESULTS'       => 'No route found between the given locations.',
                    'NOT_FOUND'          => 'One or more locations could not be geocoded.',
                    'MAX_WAYPOINTS_EXCEEDED' => 'Too many waypoints (max 25 for standard API).',
                    'OVER_DAILY_LIMIT'   => 'API key over daily request limit.',
                    'OVER_QUERY_LIMIT'   => 'Too many requests. Please wait a moment and retry.',
                    'REQUEST_DENIED'     => 'API key is invalid or missing required permissions.',
                    default              => 'Directions API error: ' . $data['status'],
                };
                return ['optimized_order' => array_keys($waypoints), 'polyline' => null, 'legs' => [], 'error' => $msg];
            }

            $route = $data['routes'][0];

            return [
                'optimized_order' => $route['waypoint_order'] ?? array_keys($waypoints),
                'polyline'        => $route['overview_polyline']['points'] ?? null,
                'legs'            => $route['legs'] ?? [],
                'bounds'          => $route['bounds'] ?? null,
                'error'           => null,
            ];

        } catch (\Exception $e) {
            Log::error('GoogleMapsService::optimizeRoute error', ['message' => $e->getMessage()]);
            return ['optimized_order' => array_keys($waypoints), 'polyline' => null, 'legs' => [], 'error' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Geocode a text address → ['lat', 'lng', 'formatted_address'] or null on failure.
     */
    public function geocode(string $address): ?array
    {
        if (!$this->isConfigured()) return null;

        try {
            $response = Http::timeout(10)->get($this->geocodeBaseUrl, [
                'address'  => $address,
                'region'   => 'PK',   // Pakistan bias
                'key'      => $this->apiKey,
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $result   = $data['results'][0];
                $location = $result['geometry']['location'];
                return [
                    'lat'               => $location['lat'],
                    'lng'               => $location['lng'],
                    'formatted_address' => $result['formatted_address'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('GoogleMapsService::geocode error', ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get the API key for safe injection into Blade (server-side only, never bundled in JS assets).
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Autocomplete suggestions via Places API (New) — server-side, no referrer restrictions.
     *
     * @return array  [['place_id'=>string, 'description'=>string, 'main_text'=>string, 'secondary'=>string], ...]
     */
    public function placesAutocomplete(string $input, string $region = 'pk'): array
    {
        if (!$this->isConfigured() || strlen(trim($input)) < 2) {
            return [];
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Content-Type'   => 'application/json',
                    'X-Goog-Api-Key' => $this->apiKey,
                ])
                ->post('https://places.googleapis.com/v1/places:autocomplete', [
                    'input'               => $input,
                    'includedRegionCodes' => [strtolower($region)],
                ]);

            if (!$response->successful()) {
                Log::warning('[Maps] Autocomplete error ' . $response->status() . ': ' . $response->body());
                return [];
            }

            $suggestions = [];
            foreach ($response->json('suggestions', []) as $s) {
                $pp = $s['placePrediction'] ?? null;
                if (!$pp) continue;
                $suggestions[] = [
                    'place_id'    => $pp['placeId'] ?? '',
                    'description' => $pp['text']['text'] ?? '',
                    'main_text'   => $pp['structuredFormat']['mainText']['text']      ?? ($pp['text']['text'] ?? ''),
                    'secondary'   => $pp['structuredFormat']['secondaryText']['text'] ?? '',
                ];
            }
            return $suggestions;

        } catch (\Exception $e) {
            Log::warning('[Maps] placesAutocomplete exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch lat/lng + formatted address for a place_id via Places API (New).
     *
     * @return array|null  ['lat'=>float, 'lng'=>float, 'address'=>string] or null on failure
     */
    public function placeDetails(string $placeId): ?array
    {
        if (!$this->isConfigured() || !$placeId) {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-Goog-Api-Key'  => $this->apiKey,
                    'X-Goog-FieldMask' => 'id,displayName,formattedAddress,location',
                ])
                ->get("https://places.googleapis.com/v1/places/{$placeId}");

            if (!$response->successful()) {
                Log::warning('[Maps] Place details error ' . $response->status());
                return null;
            }

            $data = $response->json();
            if (!isset($data['location'])) {
                return null;
            }

            return [
                'lat'     => $data['location']['latitude'],
                'lng'     => $data['location']['longitude'],
                'address' => $data['formattedAddress'] ?? ($data['displayName']['text'] ?? ''),
            ];

        } catch (\Exception $e) {
            Log::warning('[Maps] placeDetails exception: ' . $e->getMessage());
            return null;
        }
    }
}
