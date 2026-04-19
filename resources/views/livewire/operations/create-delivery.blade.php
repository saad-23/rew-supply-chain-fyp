<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Create Delivery</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Schedule product deliveries to customers</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-md mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <form wire:submit="save">
                <!-- Product Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Select Product *</label>
                    <select wire:model="product_id" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->current_stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Quantity -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Quantity *</label>
                    <input type="number" wire:model="quantity" min="1" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="1">
                    @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Customer Name *</label>
                    <input type="text" wire:model="customer_name" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="e.g. John Doe">
                    @error('customer_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Delivery Address *</label>
                    <div class="flex space-x-2">
                        <div class="relative w-full rounded-md shadow-sm">
                            <input type="text" 
                                   wire:model.live.debounce.500ms="address" 
                                   placeholder="Enter customer address" 
                                   autocomplete="off"
                                   class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150">
                            
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
                        
                        <button type="button" wire:click="geocodeAddress" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-4 py-2 rounded-lg text-sm transition font-medium whitespace-nowrap flex items-center gap-2">
                             <svg wire:loading.remove wire:target="geocodeAddress" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                            <span wire:loading.remove wire:target="geocodeAddress">Locate</span>
                            
                            <svg wire:loading wire:target="geocodeAddress" class="animate-spin h-4 w-4 text-indigo-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading wire:target="geocodeAddress">...</span>
                        </button>
                    </div>
                    @if($resolved_address)
                        <div class="mt-2 text-xs text-green-600 flex items-start gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Location confirmed: {{ $resolved_address }}</span>
                        </div>
                    @endif
                    @error('address') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    @error('latitude') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    @error('longitude') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Delivery Notes (Optional)</label>
                    <textarea wire:model="notes" rows="2" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="Any special instructions..."></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $delivery->priority == 2 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $delivery->customer_name }}
                                </p>
                                @if($delivery->product)
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400">
                                        {{ $delivery->product->name }} × {{ $delivery->quantity }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $delivery->address }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500 text-right">
                                <div>{{ $delivery->delivery_date->format('M d') }}</div>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full
                                    @if($delivery->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                    @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
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
