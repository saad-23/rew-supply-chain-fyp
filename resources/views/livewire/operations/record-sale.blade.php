<div class="p-6">
    <div class="page-header">
        <div>
            <h1 class="page-title">Record Sales</h1>
            <p class="page-subtitle">Log transactions to feed the demand forecasting engine</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form Card -->
        <div class="card card-body">
            <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-5">New Transaction</h2>

            @if (session()->has('message'))
                <div class="alert-success mb-5 justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('message') }}</span>
                    </div>
                    @if($lastSaleId)
                        <a href="{{ route('sales.receipt', $lastSaleId) }}" target="_blank"
                           class="btn-success btn-sm flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Receipt
                        </a>
                    @endif
                </div>
            @endif

            <form wire:submit="save" novalidate>
                {{-- Product --}}
                <div class="mb-5">
                    <label for="rs-product" class="form-label">Product <span class="text-red-500">*</span></label>
                    <select id="rs-product" wire:model.live="product_id"
                            class="select-enhanced @error('product_id') error @enderror"
                            aria-label="Select product to sell" aria-describedby="rs-product-err">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} — Rs. {{ number_format($product->price) }}</option>
                        @endforeach
                    </select>
                    @error('product_id') <p id="rs-product-err" class="field-error">{{ $message }}</p> @enderror

                    @if($selectedProductStock > 0)
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-slate-500">Unit price: <strong class="text-slate-700 dark:text-slate-200">Rs. {{ number_format($selectedProductPrice) }}</strong></span>
                            <span class="divider-v h-3 w-px bg-slate-200"></span>
                            <span class="{{ $selectedProductStock <= 10 ? 'badge-amber' : 'badge-green' }}" data-tooltip="Available stock units">{{ $selectedProductStock }} units</span>
                        </div>
                    @elseif($product_id)
                        <p class="mt-1.5 text-xs font-semibold text-red-500">This product is out of stock.</p>
                    @endif
                </div>

                {{-- Quantity --}}
                <div class="mb-5">
                    <label for="rs-qty" class="form-label">Quantity <span class="text-red-500">*</span></label>
                    <input id="rs-qty" type="number" wire:model.live="quantity"
                           min="1" max="{{ $selectedProductStock ?: 9999 }}"
                           class="input-enhanced @error('quantity') error @enderror"
                           placeholder="Enter quantity" aria-label="Sale quantity">
                    @error('quantity') <p class="field-error">{{ $message }}</p> @enderror

                    @if($estimatedTotal > 0)
                        <div class="mt-2.5 flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 px-4 py-2.5 rounded-xl border border-blue-100 dark:border-blue-800">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Estimated Total</span>
                            <span class="text-base font-bold text-blue-700 dark:text-blue-300">Rs. {{ number_format($estimatedTotal) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Date --}}
                <div class="mb-6">
                    <label for="rs-date" class="form-label">Sale Date <span class="text-red-500">*</span></label>
                    <input id="rs-date" type="date" wire:model="sale_date"
                           class="input-enhanced @error('sale_date') error @enderror"
                           aria-label="Sale date">
                    @error('sale_date') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full btn-lg" aria-label="Record this sale">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Record Sale
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <span class="btn-spinner"></span> Processing…
                    </span>
                </button>
            </form>
        </div>

        <!-- Recent Sales Log -->
        <div class="card p-6 max-h-[520px] overflow-y-auto">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4">Recent Transactions</h3>
            <ul role="list" class="space-y-3">
                @forelse($recentSales as $sale)
                <li class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="h-9 w-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-sm flex-shrink-0">
                        Rs
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $sale->product->name }}</p>
                        <p class="text-xs text-slate-400">Qty: {{ $sale->quantity }} &bull; {{ $sale->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Rs. {{ number_format($sale->total_amount) }}</p>
                        <span class="badge-green">Sold</span>
                    </div>
                </li>
                @empty
                <li class="flex flex-col items-center justify-center py-10 text-slate-400">
                    <svg class="w-10 h-10 mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm">No transactions yet</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
