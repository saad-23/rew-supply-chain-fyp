<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Products Inventory</h1>
            <p class="page-subtitle">Manage your product catalog, prices, and stock levels</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add New Product
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card kpi-card flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Products</p>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $products->total() }}</p>
            </div>
            <div class="kpi-icon bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="card kpi-card flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Low / Out of Stock</p>
                <p class="text-3xl font-bold text-red-600">{{ \App\Models\Product::whereColumn('current_stock', '<=', 'low_stock_threshold')->count() }}</p>
            </div>
            <div class="kpi-icon bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        <div class="card kpi-card flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Stock Value</p>
                <p class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-1">Rs. {{ number_format(\App\Models\Product::sum(\DB::raw('price * current_stock'))) }}</p>
            </div>
            <div class="kpi-icon bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card card-body mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="search"
                   class="input-enhanced pl-9"
                   placeholder="Search by name, SKUâ€¦"
                   aria-label="Search products">
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-3 flex items-center">
                <span class="btn-spinner text-blue-500"></span>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Sort by</span>
            <select wire:model.live="sortBy" class="select-enhanced w-auto min-w-[140px]" aria-label="Sort products by">
                <option value="name">Name</option>
                <option value="sku">SKU</option>
                <option value="current_stock">Stock Level</option>
                <option value="price">Price</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="data-table-wrapper" role="region" aria-label="Products table">
        <div class="overflow-x-auto">
            <table class="data-table" aria-label="Products inventory list">
                <thead>
                    <tr>
                        <th class="sticky-col" scope="col" wire:click="sortBy('name')" role="columnheader" aria-sort="{{ $sortBy === 'name' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            Product
                            @if ($sortBy === 'name') <span class="ml-1 text-blue-500">{{ $sortDirection === 'asc' ? 'â†‘' : 'â†“' }}</span> @endif
                        </th>
                        <th scope="col">Category</th>
                        <th scope="col" wire:click="sortBy('sku')" aria-sort="{{ $sortBy === 'sku' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            SKU @if ($sortBy === 'sku') <span class="ml-1 text-blue-500">{{ $sortDirection === 'asc' ? 'â†‘' : 'â†“' }}</span> @endif
                        </th>
                        <th scope="col" wire:click="sortBy('current_stock')" aria-sort="{{ $sortBy === 'current_stock' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            Stock @if ($sortBy === 'current_stock') <span class="ml-1 text-blue-500">{{ $sortDirection === 'asc' ? 'â†‘' : 'â†“' }}</span> @endif
                        </th>
                        <th scope="col" wire:click="sortBy('price')" aria-sort="{{ $sortBy === 'price' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            Price @if ($sortBy === 'price') <span class="ml-1 text-blue-500">{{ $sortDirection === 'asc' ? 'â†‘' : 'â†“' }}</span> @endif
                        </th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                {{-- Skeleton tbody: visible only while loading --}}
                <tbody wire:loading wire:target="search,sortBy">
                    @for($i=0; $i<5; $i++)
                    <tr>
                        <td><div class="flex items-center gap-3"><div class="skeleton w-9 h-9 rounded-xl flex-shrink-0"></div><div class="skeleton-text w-32"></div></div></td>
                        <td><div class="skeleton-text w-20"></div></td>
                        <td><div class="skeleton-text w-16"></div></td>
                        <td><div class="skeleton-text w-12"></div></td>
                        <td><div class="skeleton-text w-16"></div></td>
                        <td><div class="skeleton h-5 w-16 rounded-full"></div></td>
                        <td><div class="skeleton h-7 w-16 rounded-lg"></div></td>
                    </tr>
                    @endfor
                </tbody>

                {{-- Data tbody: hidden while loading --}}
                <tbody wire:loading.remove wire:target="search,sortBy">
                    @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200 text-sm">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $product->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-blue">{{ $product->category->name ?? 'Uncategorized' }}</span>
                        </td>
                        <td>
                            <code class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-md font-mono">{{ $product->sku }}</code>
                        </td>
                        <td>
                            <div class="font-semibold text-sm text-slate-700 dark:text-slate-300 mb-1">{{ number_format($product->current_stock) }}</div>
                            @php $pct = $product->low_stock_threshold > 0 ? min(100, round(($product->current_stock / max($product->low_stock_threshold * 3, 1)) * 100)) : 100; @endphp
                            <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $product->current_stock === 0 ? 'bg-red-500' : ($product->isLowStock() ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </td>
                        <td class="font-semibold text-slate-700 dark:text-slate-300">Rs. {{ number_format($product->price) }}</td>
                        <td>
                            @if($product->current_stock === 0)
                                <span class="badge-red">Out of Stock</span>
                            @elseif($product->isLowStock())
                                <span class="badge-amber">Low Stock</span>
                            @else
                                <span class="badge-green">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="btn-icon" aria-label="Edit {{ $product->name }}" data-tooltip="Edit product">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button wire:click="$dispatch('swal:confirm', { title: 'Delete Product?', text: 'This action cannot be undone.', type: 'warning', method: 'delete-confirmed', id: {{ $product->id }} })"
                                        class="btn-icon text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                        aria-label="Delete {{ $product->name }}" data-tooltip="Delete product">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-200 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="font-semibold text-slate-700 dark:text-slate-300">No products found</p>
                                <p class="text-sm text-slate-400 mt-1">Try adjusting your search or add a new product.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $products->links() }}
        </div>
    </div>
</div>
