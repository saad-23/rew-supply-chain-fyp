<div class="p-6" id="delivery-root">
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Create Delivery</h1>
            <p class="page-subtitle">Schedule product deliveries to customers</p>
        </div>
        <a href="{{ route('operations.manage-deliveries') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            All Deliveries
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">

        {{-- ---- LEFT: Form (2/5) -------------------------------- --}}
        <div class="xl:col-span-2 card card-body">

            @if (session()->has('message'))
                <div class="alert-success mb-5">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <form wire:submit="save" novalidate>

                {{-- Product --}}
                <div class="mb-4">
                    <label class="form-label">Product <span class="text-red-500">*</span></label>
                    <select wire:model="product_id" class="select-enhanced @error('product_id') error @enderror">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->current_stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Quantity --}}
                <div class="mb-4">
                    <label class="form-label">Quantity <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" wire:model="quantity" min="1"
                               class="input-enhanced pr-12 @error('quantity') error @enderror"
                               placeholder="1">
                        <span class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">pcs</span>
                    </div>
                    @error('quantity') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Customer Name --}}
                <div class="mb-4">
                    <label class="form-label">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="customer_name"
                           class="input-enhanced @error('customer_name') error @enderror"
                           placeholder="e.g. Ahmed Khan" autocomplete="off">
                    @error('customer_name') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Origin / Warehouse Address --}}
                <div class="mb-4">
                    <label class="form-label">
                        Origin / Warehouse
                        <span class="ml-1 text-xs font-normal text-slate-400">(editable)</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="origin-input"
                               class="input-enhanced"
                               placeholder="Search warehouse location…"
                               autocomplete="off">
                        <ul id="origin-input-dropdown" class="ac-dropdown hidden"></ul>
                    </div>
                    <p id="origin-confirm" class="{{ $origin_lat ? '' : 'hidden' }} mt-1 text-xs text-blue-600 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span id="origin-confirm-text">{{ $origin_address }}</span>
                    </p>
                </div>

                {{-- Destination / Delivery Address --}}
                <div class="mb-4">
                    <label class="form-label">
                        Delivery Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="dest-input"
                               class="input-enhanced"
                               placeholder="Search delivery address…"
                               autocomplete="off"
                               value="{{ $resolved_address ?? '' }}">
                        <ul id="dest-input-dropdown" class="ac-dropdown hidden"></ul>
                    </div>
                    @if($resolved_address)
                        <p class="mt-1 text-xs text-emerald-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $resolved_address }}
                        </p>
                    @endif
                    @error('latitude')  <p class="field-error">{{ $message }}</p> @enderror
                    @error('address')   <p class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label class="form-label">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea wire:model="notes" rows="2"
                              class="input-enhanced @error('notes') error @enderror"
                              placeholder="Special instructions…"></textarea>
                    @error('notes') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Date + Priority --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="form-label">Delivery Date</label>
                        <input type="date" wire:model="delivery_date"
                               class="input-enhanced @error('delivery_date') error @enderror">
                        @error('delivery_date') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Priority</label>
                        <select wire:model="priority" class="select-enhanced">
                            <option value="1">Normal</option>
                            <option value="2">High</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full btn-lg">
                    <span wire:loading.remove wire:target="save" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Schedule Delivery
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                        <span class="btn-spinner"></span> Scheduling…
                    </span>
                </button>

            </form>
        </div>

        {{-- ---- RIGHT: Map + Recent (3/5) ------------------------ --}}
        <div class="xl:col-span-3 flex flex-col gap-5">

            {{-- Map Card --}}
            <div class="card p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500">Route Preview</h3>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span class="flex items-center gap-1">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-600 ring-2 ring-white"></span> Warehouse
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span> Customer
                        </span>
                    </div>
                </div>

                @if(!$mapsReady)
                    <div class="flex flex-col items-center justify-center text-slate-400 gap-2 py-16">
                        <svg class="w-12 h-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <p class="text-sm font-medium text-slate-500">Map unavailable</p>
                        <p class="text-xs text-center max-w-xs text-slate-400">Set <code class="font-mono bg-slate-100 dark:bg-slate-700 px-1 rounded text-slate-600">GOOGLE_MAPS_API_KEY</code> in <code class="font-mono bg-slate-100 dark:bg-slate-700 px-1 rounded text-slate-600">.env</code></p>
                    </div>
                @else
                    <div id="delivery-map" class="w-full rounded-xl" style="height:380px;" wire:ignore></div>

                    <div id="route-info" class="hidden rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 px-4 py-3">
                        <div class="flex items-center justify-around gap-2 text-center">
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Distance</div>
                                <div id="route-distance" class="text-base font-bold text-blue-600 dark:text-blue-400">—</div>
                            </div>
                            <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Est. Time</div>
                                <div id="route-time" class="text-base font-bold text-emerald-600 dark:text-emerald-400">—</div>
                            </div>
                            <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Via</div>
                                <div id="route-via" class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">—</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Recent Deliveries Card --}}
            <div class="card card-body flex-1">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Recent Deliveries</h4>
                <ul class="space-y-2">
                    @forelse($recentDeliveries as $delivery)
                    <li class="flex items-start gap-2.5 py-2 border-b border-slate-50 dark:border-slate-800 last:border-0">
                        <span class="mt-1.5 flex-shrink-0 inline-block w-2 h-2 rounded-full
                            @if($delivery->status === 'pending')        bg-amber-400
                            @elseif($delivery->status === 'in_transit') bg-blue-400
                            @elseif($delivery->status === 'delivered')  bg-emerald-400
                            @else bg-red-400 @endif"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $delivery->customer_name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $delivery->address }}</p>
                            @if($delivery->product)
                                <p class="text-xs text-slate-400">{{ $delivery->product->name }} &times; {{ $delivery->quantity }}</p>
                            @endif
                        </div>
                        <span class="flex-shrink-0 text-xs text-slate-400 whitespace-nowrap">{{ $delivery->delivery_date->format('M d') }}</span>
                    </li>
                    @empty
                        <li class="text-slate-400 py-6 text-center text-sm">No recent deliveries</li>
                    @endforelse
                </ul>
            </div>

        </div>{{-- /right --}}
    </div>{{-- /grid --}}

</div>{{-- /delivery-root --}}

@if($mapsReady)
@push('scripts')
<style>
/* -- Custom autocomplete dropdown -- */
.ac-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
    background: #fff; border: 1px solid #cbd5e1; border-top: 0;
    border-radius: 0 0 .5rem .5rem;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    max-height: 220px; overflow-y: auto;
    list-style: none; padding: .25rem 0; margin: 0;
}
.ac-dropdown.hidden { display: none; }
.ac-item { padding: .45rem .75rem; cursor: pointer; display: flex; flex-direction: column; gap: 1px; }
.ac-item:hover, .ac-item:focus { background: #f1f5f9; outline: none; }
.ac-main      { font-size: .875rem; color: #1e293b; font-weight: 500; }
.ac-secondary { font-size: .75rem;  color: #94a3b8; }
.dark .ac-dropdown { background: #1e293b; border-color: #334155; }
.dark .ac-item:hover { background: #334155; }
.dark .ac-main { color: #f1f5f9; }
</style>
<script>
(function () {
    var _cfg = {
        mapsApiKey: @json($mapsApiKey),
        originLat:  {{ $origin_lat }},
        originLng:  {{ $origin_lng }},
        originAddr: @json($origin_address),
        destAddr:   @json($resolved_address ?? ''),
    };

    window.cdMap = {
        map:          null,
        originMk:     null,
        destMk:       null,
        polyline:     null,
        hasOrigin:    false,
        hasDest:      false,
        originLatLng: { lat: _cfg.originLat, lng: _cfg.originLng },
        destLatLng:   null,
        originAddr:   _cfg.originAddr,
        destAddr:     _cfg.destAddr,
    };

    /* -- Wire helper -- */
    function getWire() {
        var el = document.getElementById('delivery-root');
        if (!el) return null;
        var id = el.getAttribute('wire:id');
        return id ? window.Livewire.find(id) : null;
    }

    /* -- 1. Load Maps SDK -- */
    function loadSDK() {
        if (window.google && window.google.maps) { boot(); return; }
        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js'
              + '?key='       + encodeURIComponent(_cfg.mapsApiKey)
              + '&v=weekly'
              + '&libraries=marker,geometry'
              + '&loading=async'
              + '&callback=cdMapInit';
        s.onerror = function () { console.error('[DeliveryMap] SDK load failed.'); };
        document.head.appendChild(s);
    }

    window.cdMapInit = function () { boot(); };

    /* -- 2. Init map -- */
    function boot() {
        var m  = window.cdMap;
        var el = document.getElementById('delivery-map');
        if (!el || m.map) return;

        m.map = new google.maps.Map(el, {
            center:            m.originLatLng,
            zoom:              11,
            mapId:             'DEMO_MAP_ID',
            mapTypeControl:    false,
            streetViewControl: false,
            fullscreenControl: false,
        });

        placeMarker('origin', m.originLatLng.lat, m.originLatLng.lng, m.originAddr);
        m.hasOrigin = true;

        /* Pre-populate origin input */
        var oi = document.getElementById('origin-input');
        if (oi && !oi.value && m.originAddr) { oi.value = m.originAddr; }

        attachAC('origin-input', onOriginPicked);
        attachAC('dest-input',   onDestPicked);

        /* Restore dest pin if coords already set (post-validation) */
        if (m.destAddr) {
            geocodeAddress(m.destAddr, function (latlng) {
                if (!latlng) return;
                m.destLatLng = { lat: latlng.lat(), lng: latlng.lng() };
                placeMarker('dest', m.destLatLng.lat, m.destLatLng.lng, m.destAddr);
                m.hasDest = true;
                fitBothMarkers();
                drawRoute();
            });
        }
    }

    /* -- 3. Geocode helper -- */
    function geocodeAddress(address, cb) {
        var gc = new google.maps.Geocoder();
        gc.geocode({ address: address, region: 'PK' }, function (res, status) {
            cb((status === 'OK' && res.length) ? res[0].geometry.location : null);
        });
    }

    /* -- 4. Custom autocomplete via Laravel proxy -- */
    function attachAC(inputId, callback) {
        var inp = document.getElementById(inputId);
        var dd  = document.getElementById(inputId + '-dropdown');
        if (!inp || !dd) return;

        var timer = null;

        inp.addEventListener('input', function () {
            var q = inp.value.trim();
            clearTimeout(timer);
            if (q.length < 2) { closeDD(dd); return; }
            timer = setTimeout(function () { fetchSuggestions(q, dd, inp, callback); }, 280);
        });

        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeDD(dd); }
            if (e.key === 'ArrowDown') {
                var first = dd.querySelector('.ac-item');
                if (first) { e.preventDefault(); first.focus(); }
            }
        });

        /* Keyboard navigation inside dropdown */
        dd.addEventListener('keydown', function (e) {
            var items = dd.querySelectorAll('.ac-item');
            var idx   = Array.prototype.indexOf.call(items, document.activeElement);
            if (e.key === 'ArrowDown' && idx < items.length - 1) { e.preventDefault(); items[idx + 1].focus(); }
            if (e.key === 'ArrowUp')   { e.preventDefault(); idx > 0 ? items[idx - 1].focus() : inp.focus(); }
            if (e.key === 'Escape')    { closeDD(dd); inp.focus(); }
        });

        document.addEventListener('mousedown', function (e) {
            if (!inp.contains(e.target) && !dd.contains(e.target)) { closeDD(dd); }
        });
    }

    function fetchSuggestions(q, dd, inp, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/maps/autocomplete?q=' + encodeURIComponent(q));
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            var items;
            try { items = JSON.parse(xhr.responseText); } catch (e) { return; }
            renderDD(items, dd, inp, callback);
        };
        xhr.send();
    }

    function renderDD(items, dd, inp, callback) {
        dd.innerHTML = '';
        if (!items || !items.length) { closeDD(dd); return; }
        items.forEach(function (item) {
            var li = document.createElement('li');
            li.className = 'ac-item';
            li.tabIndex  = 0;
            li.innerHTML = '<span class="ac-main">'      + esc(item.main_text)  + '</span>'
                         + (item.secondary
                             ? '<span class="ac-secondary">' + esc(item.secondary) + '</span>'
                             : '');
            var handler = function (e) {
                e.preventDefault();
                inp.value = item.description;
                closeDD(dd);
                fetchPlaceDetails(item.place_id, item.description, callback);
            };
            li.addEventListener('mousedown', handler);
            li.addEventListener('keydown', function (e) { if (e.key === 'Enter') handler(e); });
            dd.appendChild(li);
        });
        dd.classList.remove('hidden');
    }

    function fetchPlaceDetails(placeId, fallbackAddr, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/maps/place?id=' + encodeURIComponent(placeId));
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            var data;
            try { data = JSON.parse(xhr.responseText); } catch (e) { return; }
            if (data && data.lat && data.lng) {
                callback(parseFloat(data.lat), parseFloat(data.lng), data.address || fallbackAddr);
            }
        };
        xhr.send();
    }

    function closeDD(dd) { dd.classList.add('hidden'); dd.innerHTML = ''; }
    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* -- 5. Selection callbacks -- */
    function onOriginPicked(lat, lng, address) {
        var m    = window.cdMap;
        var wire = getWire();
        if (wire) wire.call('setOriginCoords', lat, lng, address);
        m.originLatLng = { lat: lat, lng: lng };
        m.originAddr   = address;
        m.hasOrigin    = true;
        placeMarker('origin', lat, lng, address);
        var cfm = document.getElementById('origin-confirm');
        var txt = document.getElementById('origin-confirm-text');
        if (cfm && txt) { txt.textContent = address; cfm.classList.remove('hidden'); }
        if (m.hasDest) { fitBothMarkers(); drawRoute(); }
        else { m.map.panTo({ lat: lat, lng: lng }); m.map.setZoom(13); }
    }

    function onDestPicked(lat, lng, address) {
        var m    = window.cdMap;
        var wire = getWire();
        if (wire) wire.call('setDestinationCoords', lat, lng, address);
        m.destLatLng = { lat: lat, lng: lng };
        m.destAddr   = address;
        m.hasDest    = true;
        placeMarker('dest', lat, lng, address);
        if (m.hasOrigin) { fitBothMarkers(); drawRoute(); }
        else { m.map.panTo({ lat: lat, lng: lng }); m.map.setZoom(13); }
    }

    /* -- 6. fitBounds helper -- */
    function fitBothMarkers() {
        var m = window.cdMap;
        if (!m.originLatLng || !m.destLatLng) return;
        var b = new google.maps.LatLngBounds();
        b.extend(m.originLatLng);
        b.extend(m.destLatLng);
        m.map.fitBounds(b, 80);
    }

    /* -- 7. AdvancedMarkerElement + PinElement -- */
    function placeMarker(type, lat, lng, label) {
        var m        = window.cdMap;
        var isOrigin = (type === 'origin');
        var color    = isOrigin ? '#2563eb' : '#10b981';
        var title    = isOrigin ? 'Warehouse' : 'Delivery';

        var pin = new google.maps.marker.PinElement({
            background:  color,
            borderColor: '#ffffff',
            glyphColor:  '#ffffff',
            scale:       1.2,
        });

        var mk = new google.maps.marker.AdvancedMarkerElement({
            position: { lat: lat, lng: lng },
            map:      m.map,
            title:    label || title,
            content:  pin,
        });

        mk.addEventListener('gmp-click', function () {
            var iw = new google.maps.InfoWindow({
                content: '<div style="font-size:12px;padding:4px 8px;max-width:220px;">'
                       + '<strong>' + title + '</strong><br>'
                       + '<span style="color:#64748b;font-size:11px;">' + esc(label || '') + '</span>'
                       + '</div>',
            });
            iw.open(m.map, mk);
        });

        if (isOrigin) {
            if (m.originMk) { m.originMk.map = null; }
            m.originMk = mk;
        } else {
            if (m.destMk) { m.destMk.map = null; }
            m.destMk = mk;
        }
    }

    /* -- 8. Routes API (XHR, server-generated polyline) -- */
    function drawRoute() {
        var m = window.cdMap;
        if (!m.originLatLng || !m.destLatLng) return;

        var body = JSON.stringify({
            origin:      { location: { latLng: { latitude: m.originLatLng.lat, longitude: m.originLatLng.lng } } },
            destination: { location: { latLng: { latitude: m.destLatLng.lat,   longitude: m.destLatLng.lng   } } },
            travelMode:  'DRIVE',
            routingPreference: 'TRAFFIC_AWARE',
            computeAlternativeRoutes: false,
        });

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'https://routes.googleapis.com/directions/v2:computeRoutes');
        xhr.setRequestHeader('Content-Type',     'application/json');
        xhr.setRequestHeader('X-Goog-Api-Key',   _cfg.mapsApiKey);
        xhr.setRequestHeader('X-Goog-FieldMask', 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline');
        xhr.onload = function () {
            if (xhr.status !== 200) {
                console.warn('[DeliveryMap] Routes API error', xhr.status, xhr.responseText);
                return;
            }
            var data;
            try { data = JSON.parse(xhr.responseText); } catch (e) { return; }
            if (!data.routes || !data.routes[0]) return;

            var route  = data.routes[0];
            var distKm = (route.distanceMeters / 1000).toFixed(1) + ' km';
            var sec    = parseInt((route.duration || '0s').replace('s', ''), 10);
            var min    = Math.round(sec / 60);
            var durTxt = (min < 60) ? (min + ' mins')
                       : (Math.floor(min / 60) + ' hr ' + (min % 60) + ' min');

            var dEl = document.getElementById('route-distance');
            var tEl = document.getElementById('route-time');
            var vEl = document.getElementById('route-via');
            var pEl = document.getElementById('route-info');
            if (dEl) dEl.textContent = distKm;
            if (tEl) tEl.textContent = durTxt;
            if (vEl) vEl.textContent = 'Driving Route';
            if (pEl) pEl.classList.remove('hidden');

            if (route.polyline && route.polyline.encodedPolyline) {
                drawPolyline(route.polyline.encodedPolyline);
            }
        };
        xhr.onerror = function () { console.warn('[DeliveryMap] Routes API network error'); };
        xhr.send(body);
    }

    /* -- 9. Polyline -- */
    function drawPolyline(encoded) {
        var m    = window.cdMap;
        var path = google.maps.geometry.encoding.decodePath(encoded);
        if (m.polyline) { m.polyline.setMap(null); }
        m.polyline = new google.maps.Polyline({
            path:          path,
            geodesic:      true,
            strokeColor:   '#2563eb',
            strokeOpacity: 0.85,
            strokeWeight:  4,
        });
        m.polyline.setMap(m.map);
        var b = new google.maps.LatLngBounds();
        for (var i = 0; i < path.length; i++) { b.extend(path[i]); }
        m.map.fitBounds(b, 60);
    }

    /* -- 10. Livewire SPA navigation -- */
    document.addEventListener('livewire:navigated', function () {
        if (window.cdMap) window.cdMap.map = null;
        if (window.google && window.google.maps) { boot(); }
    });

    /* -- 11. Boot on Livewire ready -- */
    document.addEventListener('livewire:initialized', function () { loadSDK(); });
    if (window.Livewire) { loadSDK(); }

})();
</script>
@endpush
@endif

