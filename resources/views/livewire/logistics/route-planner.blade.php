<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Route Optimization</h1>
            <p class="page-subtitle">Plan efficient delivery routes with Google Maps</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="date" wire:model.live="date"
                   class="input-enhanced cursor-pointer"
                   aria-label="Select delivery date">
            <button wire:click="optimize" class="btn-primary"
                    wire:loading.attr="disabled"
                    data-tooltip="Optimize delivery sequence using Google Directions API">
                <span wire:loading.remove wire:target="optimize" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Optimize Route
                </span>
                <span wire:loading wire:target="optimize" class="flex items-center gap-2">
                    <span class="btn-spinner"></span> Optimizing…
                </span>
            </button>
        </div>
    </div>

    {{-- API key not configured warning --}}
    @if(!$mapsReady)
        <div class="alert-warning mb-5 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span><strong>Google Maps API key not set.</strong> Add <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">GOOGLE_MAPS_API_KEY=your_key</code> to <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">.env</code>, then run <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">php artisan config:clear</code>.</span>
        </div>
    @endif

    {{-- Route error --}}
    @if($routeError)
        <div class="alert-warning mb-5 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ $routeError }}</span>
        </div>
    @endif


    <!-- Main Grid: Sidebar + Map -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Delivery Queue Sidebar -->
        <div class="card p-4 h-[620px] overflow-y-auto">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-3 sticky top-0 bg-white dark:bg-slate-800 pb-2 border-b border-slate-100 dark:border-slate-700 z-10">
                Delivery Queue
                <span class="badge-blue ml-2">{{ count(!empty($optimizedRoutes) ? $optimizedRoutes : $deliveries) }} Stops</span>
            </h3>

            @php $list = !empty($optimizedRoutes) ? $optimizedRoutes : $deliveries; @endphp

            @forelse($list as $index => $delivery)
                @php
                    $d = is_array($delivery) ? (object)$delivery : $delivery;
                    $isOptimized = !empty($optimizedRoutes);
                @endphp
                <div class="mb-3 p-3 border rounded-xl transition-colors cursor-pointer
                    {{ $isOptimized ? 'border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-800' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}"
                     onclick="focusMarker({{ $loop->index }})">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $isOptimized ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $d->customer_name }}</h4>
                            <p class="text-xs text-slate-400 truncate">{{ $d->address }}</p>
                            <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                                <span class="badge-blue">#{{ $d->id }}</span>
                                @if($isOptimized && isset($routeLegs[$loop->index]))
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                        {{ $routeLegs[$loop->index]['distance']['text'] ?? '' }}
                                        &middot; {{ $routeLegs[$loop->index]['duration']['text'] ?? '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                    <svg class="w-10 h-10 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <p class="text-sm mb-3">No deliveries found for this date.</p>
                    <button wire:click="loadSampleData" wire:loading.attr="disabled" class="btn-secondary btn-sm">
                        <span wire:loading.remove wire:target="loadSampleData">+ Load Sample Data</span>
                        <span wire:loading wire:target="loadSampleData" class="flex items-center gap-1.5"><span class="btn-spinner"></span> Loading…</span>
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Google Map -->
        <div class="lg:col-span-2 card p-1 h-[620px] relative z-10">
            @if(!$mapsReady)
                <div class="w-full h-full rounded-xl bg-slate-100 dark:bg-slate-800 flex flex-col items-center justify-center text-slate-400 gap-3">
                    <svg class="w-16 h-16 opacity-25" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <p class="font-semibold text-slate-500">Map requires Google Maps API key</p>
                    <p class="text-sm">Set <code class="font-mono">GOOGLE_MAPS_API_KEY</code> in .env</p>
                </div>
            @else
                <div id="gmap" class="w-full h-full rounded-xl outline-none" wire:ignore></div>
            @endif
        </div>
    </div>

    {{-- Securely inject API key via meta — never hardcoded in JS asset files --}}
    @if($mapsReady)
        <meta name="gmaps-key" content="{{ $mapsApiKey }}">

        @script
        <script>
            const WAREHOUSE = @json($warehouse);
            let gmap = null;
            let warehouseInfoWindow = null;
            let stopMarkers = [];
            let allInfoWindows = [];
            let routePolyline = null;

            // ── Load SDK ─────────────────────────────────────────────────────
            (function loadGoogleMaps() {
                if (window.google?.maps) { initGMap(); return; }
                const key = document.querySelector('meta[name="gmaps-key"]')?.content || '';
                const s = document.createElement('script');
                s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=geometry&callback=initGMap';
                s.async = true;
                s.defer = true;
                s.onerror = () => console.error('[RoutePlanner] Failed to load Google Maps SDK.');
                document.head.appendChild(s);
            })();

            // ── Init base map ─────────────────────────────────────────────────
            window.initGMap = function () {
                const el = document.getElementById('gmap');
                if (!el) return;

                gmap = new google.maps.Map(el, {
                    center: { lat: WAREHOUSE.lat, lng: WAREHOUSE.lng },
                    zoom: 12,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                // Warehouse — blue circle marker
                const whMarker = new google.maps.Marker({
                    position: { lat: WAREHOUSE.lat, lng: WAREHOUSE.lng },
                    map: gmap,
                    title: WAREHOUSE.name,
                    icon: { path: google.maps.SymbolPath.CIRCLE, scale: 13, fillColor: '#2563eb', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 3 },
                    zIndex: 9999,
                });

                warehouseInfoWindow = new google.maps.InfoWindow({
                    content: `<div style="font-size:13px;padding:4px 6px;"><strong>🏭 ${WAREHOUSE.name}</strong><br><span style="color:#64748b;font-size:11px;">Starting point</span></div>`
                });
                whMarker.addListener('click', () => { closeAll(); warehouseInfoWindow.open(gmap, whMarker); });
                allInfoWindows.push(warehouseInfoWindow);

                // Render any pre-loaded data (e.g. page reload with optimized routes)
                const preloaded = @json(!empty($optimizedRoutes) ? array_values($optimizedRoutes) : []);
                const prePolyline = @json($routePolyline);
                if (preloaded.length) renderStops(preloaded, prePolyline);
            };

            // ── Clear map overlays ───────────────────────────────────────────
            function clearMap() {
                stopMarkers.forEach(m => m.setMap(null));
                stopMarkers = [];
                allInfoWindows.slice(1).forEach(iw => iw.close()); // keep [0] = warehouseInfoWindow
                allInfoWindows = allInfoWindows.slice(0, 1);
                if (routePolyline) { routePolyline.setMap(null); routePolyline = null; }
            }

            function closeAll() {
                allInfoWindows.forEach(iw => iw?.close());
            }

            // ── Render numbered stop markers + polyline ──────────────────────
            function renderStops(locations, encodedPoly) {
                if (!gmap) return;
                clearMap();

                const bounds = new google.maps.LatLngBounds();
                bounds.extend({ lat: WAREHOUSE.lat, lng: WAREHOUSE.lng });

                locations.forEach((loc, i) => {
                    const lat = parseFloat(loc.latitude);
                    const lng = parseFloat(loc.longitude);
                    if (isNaN(lat) || isNaN(lng)) return;

                    const pos = { lat, lng };
                    bounds.extend(pos);

                    const marker = new google.maps.Marker({
                        position: pos,
                        map: gmap,
                        title: loc.customer_name,
                        label: { text: String(i + 1), color: '#fff', fontWeight: 'bold', fontSize: '12px' },
                        icon: { path: google.maps.SymbolPath.CIRCLE, scale: 16, fillColor: '#10b981', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2 },
                        zIndex: i + 1,
                    });

                    const iw = new google.maps.InfoWindow({
                        content: `<div style="font-size:13px;padding:4px 6px;max-width:220px;">
                            <strong>Stop ${i + 1}: ${loc.customer_name}</strong><br>
                            <span style="color:#64748b;font-size:11px;">${loc.address || ''}</span>
                        </div>`
                    });

                    marker.addListener('click', () => { closeAll(); iw.open(gmap, marker); });
                    stopMarkers.push(marker);
                    allInfoWindows.push(iw);
                });

                // Draw route — encoded polyline if available, else dashed fallback
                if (encodedPoly && window.google?.maps?.geometry) {
                    const path = google.maps.geometry.encoding.decodePath(encodedPoly);
                    routePolyline = new google.maps.Polyline({
                        path, geodesic: true,
                        strokeColor: '#6366f1', strokeOpacity: 0.85, strokeWeight: 5,
                        map: gmap,
                    });
                } else {
                    const path = [
                        { lat: WAREHOUSE.lat, lng: WAREHOUSE.lng },
                        ...locations.map(l => ({ lat: parseFloat(l.latitude), lng: parseFloat(l.longitude) })).filter(p => !isNaN(p.lat) && !isNaN(p.lng)),
                        { lat: WAREHOUSE.lat, lng: WAREHOUSE.lng },
                    ];
                    routePolyline = new google.maps.Polyline({
                        path, geodesic: true,
                        strokeColor: '#f59e0b', strokeOpacity: 0.7, strokeWeight: 4,
                        icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, scale: 3 }, offset: '0', repeat: '14px' }],
                        map: gmap,
                    });
                }

                if (!bounds.isEmpty()) gmap.fitBounds(bounds, 50);
            }

            // Click sidebar stop → pan map to that marker
            window.focusMarker = function (index) {
                const marker = stopMarkers[index];
                if (!marker || !gmap) return;
                gmap.panTo(marker.getPosition());
                gmap.setZoom(15);
                closeAll();
                allInfoWindows[index + 1]?.open(gmap, marker);
            };

            // Livewire event
            $wire.on('routes-optimized', (evt) => {
                const payload = Array.isArray(evt) ? evt[0] : evt;
                const routes  = payload.routes  || [];
                const poly    = payload.polyline || null;
                renderStops(routes, poly);
            });
        </script>
        @endscript
    @endif
</div>
