<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $productId ? 'Edit Product' : 'Add New Product' }}</h1>
            <p class="page-subtitle">{{ $productId ? 'Update product information in the catalog' : 'Register a new product in the inventory' }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form Card -->
        <div class="card card-body">
            <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-5">Product Details</h2>

            <form wire:submit="saveProduct" novalidate>
                @csrf

                {{-- Product Name --}}
                <div class="mb-5">
                    <label for="pf-name" class="form-label">Product Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <input id="pf-name" type="text" wire:model.blur="name"
                               class="input-enhanced pl-9 @error('name') error @enderror"
                               placeholder="e.g. Industrial Bearing 6204"
                               aria-label="Product name" aria-describedby="pf-name-err"
                               aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                    </div>
                    @error('name')
                        <p id="pf-name-err" class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-5">
                    <label for="pf-cat" class="form-label">Category <span class="text-red-500">*</span></label>
                    <select id="pf-cat" wire:model="category_id"
                            class="select-enhanced @error('category_id') error @enderror"
                            aria-label="Product category" aria-describedby="pf-cat-err">
                        <option value="">â€” Select Category â€”</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p id="pf-cat-err" class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SKU --}}
                <div class="mb-5">
                    <label for="sku-input" class="form-label">SKU / Barcode <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <input type="text" id="sku-input" wire:model.blur="sku"
                                   class="input-enhanced pl-9 @error('sku') error @enderror"
                                   placeholder="Scan barcode or type SKU"
                                   aria-label="Product SKU or barcode">
                        </div>
                        <button type="button" onclick="document.getElementById('sku-input').focus()"
                                class="btn-secondary btn-sm whitespace-nowrap"
                                data-tooltip="Click then scan barcode" aria-label="Activate barcode scanner">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Scan
                        </button>
                    </div>
                    @error('sku')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quantity + Price --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="pf-stock" class="form-label">Quantity <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="pf-stock" type="number" wire:model.blur="current_stock" min="0"
                                   class="input-enhanced pr-12 @error('current_stock') error @enderror"
                                   placeholder="0" aria-label="Current stock quantity">
                            <span class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">pcs</span>
                        </div>
                        @error('current_stock') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="pf-price" class="form-label">Price (PKR) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-xs font-bold text-slate-500 pointer-events-none">Rs.</span>
                            <input id="pf-price" type="number" step="0.01" wire:model.blur="price" min="0"
                                   class="input-enhanced pl-10 @error('price') error @enderror"
                                   placeholder="0.00" aria-label="Product price in PKR">
                        </div>
                        @error('price') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Low Stock Threshold --}}
                <div class="mb-6">
                    <label for="pf-threshold" class="form-label">
                        Low Stock Alert Threshold <span class="text-red-500">*</span>
                        <span class="ml-1 normal-case text-slate-400 font-normal"
                              data-tooltip="System alert fires when current stock drops to or below this number">
                            <svg class="inline w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </label>
                    <div class="relative">
                        <input id="pf-threshold" type="number" wire:model.blur="low_stock_threshold" min="1"
                               class="input-enhanced pr-16 @error('low_stock_threshold') error @enderror"
                               placeholder="10" aria-label="Low stock alert threshold in units">
                        <span class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">units</span>
                    </div>
                    @error('low_stock_threshold') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-slate-400">Alert triggers when stock â‰¤ this value</p>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full btn-lg" aria-label="{{ $productId ? 'Update product' : 'Create product' }}">
                    <span wire:loading.remove wire:target="saveProduct">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $productId ? 'Update Product' : 'Create Product' }}
                    </span>
                    <span wire:loading wire:target="saveProduct" class="flex items-center gap-2">
                        <span class="btn-spinner"></span>
                        {{ $productId ? 'Updatingâ€¦' : 'Creatingâ€¦' }}
                    </span>
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-400 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Recent Products Sidebar -->
        <div class="card p-6 h-[540px] overflow-y-auto">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4">
                {{ $productId ? 'Similar Products' : 'Recently Added' }}
            </h3>
            <ul role="list" class="space-y-3">
                @forelse($recentProducts as $product)
                <li class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($product->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-slate-400 truncate">SKU: {{ $product->sku }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Rs. {{ number_format($product->price) }}</p>
                        <span class="{{ $product->isLowStock() ? 'badge-amber' : 'badge-green' }}">
                            {{ $product->current_stock }} pcs
                        </span>
                    </div>
                </li>
                @empty
                <li class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <svg class="w-10 h-10 mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="text-sm">No products yet</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


