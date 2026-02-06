<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Record Sales</h1>
            <p class="text-sm text-gray-500">Log transactions to feed the forecasting engine</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('message') }}
                    @if($lastSaleId)
                        <div class="mt-2">
                             <a href="{{ route('sales.receipt', $lastSaleId) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Receipt
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product</label>
                    <select wire:model="product_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} (Stock: {{ $product->current_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                    <input type="number" wire:model="quantity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" wire:model="sale_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('sale_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Record Sale
                </button>
            </form>
        </div>

        <!-- Recent Sales Log -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Recent Transactions</h3>
            <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentSales as $sale)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    $
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $sale->product->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    Qty: {{ $sale->quantity }}
                                </p>
                            </div>
                            <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                {{ number_format($sale->total_amount) }}
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No sales recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
