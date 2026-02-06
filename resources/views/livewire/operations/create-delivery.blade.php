<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Schedule Delivery</h1>
            <p class="text-sm text-gray-500">Create new shipments for route optimization</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Customer Name</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" wire:model="customer_name" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="e.g. John Doe">
                    </div>
                    @error('customer_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Address</label>
                    <div class="flex space-x-2">
                        <div class="relative w-full rounded-md shadow-sm">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.500ms="address" 
                                   placeholder="Enter street address" 
                                   autocomplete="off"
                                   class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150">
                            
                            @if(!empty($suggestions))
                                <ul class="absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    @foreach($suggestions as $index => $suggestion)
                                        <li class="px-4 py-2 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer text-sm border-b dark:border-gray-700 last:border-none"
                                            wire:click="selectSuggestion({{ $index }})">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">{{ Str::limit($suggestion['display_name'], 60) }}</div>
                                            <div class="text-xs text-gray-500">{{ $suggestion['type'] ?? 'Location' }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        
                        <button type="button" wire:click="geocodeAddress" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-4 py-2 rounded-md text-sm transition font-medium whitespace-nowrap flex items-center gap-2">
                             <svg wire:loading.remove wire:target="geocodeAddress" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                            <span wire:loading.remove wire:target="geocodeAddress">Get Coordinates</span>
                            
                            <svg wire:loading wire:target="geocodeAddress" class="animate-spin h-4 w-4 text-indigo-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading wire:target="geocodeAddress">Locating...</span>
                        </button>
                    </div>
                    @if($resolved_address)
                        <div class="mt-2 text-xs text-green-600 flex items-start gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Found: {{ $resolved_address }}</span>
                        </div>
                    @endif
                    @error('address') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-400 mt-1">Click Get Coordinates to fetch real GPS location via OpenStreetMap.</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Latitude</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" wire:model="latitude" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150 pl-3">
                        </div>
                        @error('latitude') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Longitude</label>
                         <div class="relative rounded-md shadow-sm">
                            <input type="text" wire:model="longitude" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150 pl-3">
                         </div>
                        @error('longitude') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Date</label>
                        <input type="date" wire:model="delivery_date" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150 cursor-pointer">
                        @error('delivery_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Priority</label>
                        <select wire:model="priority" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150 cursor-pointer">
                            <option value="1">Normal</option>
                            <option value="2">High</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-[1.02]">
                    Schedule Delivery
                </button>
            </form>
        </div>

        <!-- Recent Log -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm h-[500px] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Pending Shipments</h3>
            <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentDeliveries as $delivery)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $delivery->priority == 2 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $delivery->customer_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $delivery->address }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $delivery->delivery_date->format('M d') }}
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No scheduled deliveries.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
