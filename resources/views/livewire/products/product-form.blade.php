<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $productId ? 'Edit Product' : 'Add New Product' }}</h1>
            <p class="text-sm text-gray-500">Manage catalog inventory details</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <form wire:submit="saveProduct">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Product Name <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <input type="text" wire:model="name" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="e.g. Industrial Bearing 6204">
                    </div>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">SKU / Barcode <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="relative w-full">
                            <input type="text" id="sku-input" wire:model="sku" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white pl-10 transition-all duration-150" placeholder="Scan barcode or enter SKU">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('sku-input').focus()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 whitespace-nowrap" title="Click to Focus for Scan">
                             <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            Scan
                        </button>
                    </div>
                    @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" wire:model="current_stock" class="w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="0" min="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">pcs</span>
                            </div>
                        </div>
                         @error('current_stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                         <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Price (PKR) <span class="text-red-500">*</span></label>
                         <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rs.</span>
                            </div>
                            <input type="number" step="0.01" wire:model="price" class="pl-12 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="0.00" min="0">
                         </div>
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-[1.02] flex justify-center items-center gap-2">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $productId ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium transition-colors">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Recent Products Added (Right Sidebar) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm h-[500px] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">
                {{ $productId ? 'Similar Products' : 'Recently Added Products' }}
            </h3>
            <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentProducts as $product)
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-lg">
                                    {{ substr($product->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $product->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    SKU: {{ $product->sku }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($product->price) }}
                                </div>
                                <div class="text-xs {{ $product->current_stock > 10 ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $product->current_stock }} in stock
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-gray-500">No products found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
