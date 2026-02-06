<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Route Optimization</h1>
            <p class="text-sm text-gray-500">Plan efficient delivery routes</p>
        </div>
        <div class="flex space-x-4">
            <input type="date" wire:model.live="date" class="py-3 px-4 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150 cursor-pointer">
            <button wire:click="optimize" class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white px-6 py-3 rounded-lg flex items-center shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Optimize Route
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 h-[600px] overflow-y-auto">
            <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4 sticky top-0 bg-white dark:bg-gray-800 pb-2 border-b">
                Delivery Queue 
                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full ml-2">{{ count($optimizedRoutes ?? $deliveries) }} Stops</span>
            </h3>

            @php
                $list = !empty($optimizedRoutes) ? $optimizedRoutes : $deliveries;
            @endphp

            @forelse($list as $index => $delivery)
                <div class="mb-4 p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ !empty($optimizedRoutes) ? 'border-green-200 bg-green-50 dark:bg-green-900/20' : 'border-gray-200' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start">
                             <div class="flex-shrink-0 h-8 w-8 rounded-full bg-{{ !empty($optimizedRoutes) ? 'green' : 'gray' }}-100 flex items-center justify-center text-{{ !empty($optimizedRoutes) ? 'green' : 'gray' }}-600 font-bold text-sm mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $delivery->customer_name }}</h4>
                                <p class="text-xs text-gray-500">{{ $delivery->address }}</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">#{{ $delivery->id }}</span>
                                    <span class="text-xs text-gray-400">Lat: {{ number_format($delivery->latitude, 4) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 py-10">
                    No deliveries found for this date.
                </div>
            @endforelse
        </div>

        <!-- Map Visualization -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-1 h-[600px] relative z-10">
             <div id="map" class="w-full h-full rounded-lg outline-none" wire:ignore></div>
        </div>
    </div>
    
    @assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet Routing Machine -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <style>
        .leaflet-routing-container {
            display: none !important; /* Hide the default panel to keep UI clean, or remove this to show turn-by-turn */
        }
    </style>
    @endassets

    @script
    <script>
        let map;
        let control = null;
        let markers = [];
        const warehouse = @json($warehouse);

        function initMap() {
            if (map) {
                map.remove(); // Clean up existing instance
            }

            // Default View: Warehouse
            const mapContainer = document.getElementById('map');
            if(!mapContainer) return;

            map = L.map('map').setView([warehouse.lat, warehouse.lng], 13);
            
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Add Warehouse Marker
            L.marker([warehouse.lat, warehouse.lng])
                .addTo(map)
                .bindPopup(`<b>Start: ${warehouse.name}</b>`)
                .openPopup();

            setTimeout(() => { map.invalidateSize(); }, 200);
        }

        function updateMap(locations) {
            if (!map) initMap();
            
            // Clear existing
            markers.forEach(m => map.removeLayer(m));
            if (control) {
                map.removeControl(control);
                control = null;
            }
            markers = [];

            if (!locations || locations.length === 0) return;

            let waypoints = [];
            
            // Add Warehouse as Start Point
            waypoints.push(L.latLng(warehouse.lat, warehouse.lng));

            locations.forEach((loc, index) => {
                let lat = parseFloat(loc.latitude);
                let lng = parseFloat(loc.longitude);

                // Add numbered marker
                let marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(`<b>Stop ${index + 1}: ${loc.customer_name}</b><br>${loc.address}`);
                
                markers.push(marker);
                waypoints.push(L.latLng(lat, lng));
            });

            // Use Leaflet Routing Machine to draw real road path
            control = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: false,
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: true,
                showAlternatives: false,
                lineOptions: {
                    styles: [{color: '#6366f1', opacity: 0.8, weight: 6}]
                },
                createMarker: function() { return null; } // We already added custom markers
            }).addTo(map);

            // Handle routing events to fit bounds nicely
            control.on('routesfound', function(e) {
                var routes = e.routes;
                // You can extract distance/time here if needed
                // console.log('Distance: ' + routes[0].summary.totalDistance + ' meters');
            });
        }

        // Initialize
        initMap();

        // Load initial data
        let initialData = @json(!empty($optimizedRoutes) ? $optimizedRoutes : []);
        // Convert to array if it's an object (Livewire sometimes sends objects)
        let locationsArray = Array.isArray(initialData) ? initialData : Object.values(initialData);
        
        if (locationsArray.length > 0) {
             updateMap(locationsArray);
        }

        $wire.on('routes-optimized', (event) => {
             let data = event.data || event;
             let locs = Array.isArray(data) ? data : Object.values(data);
             updateMap(locs);
        });
    </script>
    @endscript
</div>
