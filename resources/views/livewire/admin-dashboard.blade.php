<div class="p-6 space-y-8">

    <!-- Welcome Section -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Overview of your system performance and activities</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-400 bg-white dark:bg-slate-800 px-4 py-2 rounded-xl border border-slate-100 dark:border-slate-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Last updated: {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Products -->
        <div class="kpi-card group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Products</p>
                    <h3 class="text-4xl font-bold text-slate-800 dark:text-slate-100 mt-2">{{ $totalProducts }}</h3>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                @if($productChangePercent >= 0)
                    <span class="badge-green">+{{ $productChangePercent }}%</span>
                @else
                    <span class="badge-red">{{ $productChangePercent }}%</span>
                @endif
                <span class="text-slate-400 text-xs">vs last month</span>
            </div>
        </div>

        <!-- Stock Value -->
        <div class="kpi-card group" data-tooltip="Total value of all current inventory at cost price">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Stock Value</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-2 tracking-tight">
                        {{ number_format($totalStockValue, 0) }}
                        <span class="text-base font-normal text-slate-400">PKR</span>
                    </h3>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                @if($stockValueChangePercent >= 0)
                    <span class="badge-green">+{{ $stockValueChangePercent }}%</span>
                @else
                    <span class="badge-red">{{ $stockValueChangePercent }}%</span>
                @endif
                <span class="text-slate-400 text-xs">MoM change</span>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="kpi-card group" data-tooltip="Products where current stock ≤ their individual reorder threshold">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Low Stock Alerts</p>
                    <h3 class="text-4xl font-bold mt-2 {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-800 dark:text-slate-100' }}">
                        {{ $lowStockCount }}
                    </h3>
                </div>
                <div class="p-3 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                @if($lowStockCount > 0)
                    <a href="{{ route('analytics.alerts') }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm flex items-center gap-1">
                        View Alerts <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @else
                    <span class="badge-green">All stocked</span>
                @endif
            </div>
        </div>

        <!-- Pending Deliveries -->
        <div class="kpi-card group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Pending Deliveries</p>
                    <h3 class="text-4xl font-bold text-slate-800 dark:text-slate-100 mt-2">{{ $pendingDeliveries }}</h3>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm flex-wrap">
                <span class="badge-blue">{{ $pendingDeliveriesThisWeek }} this week</span>
                <a href="{{ route('logistics.routes') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center gap-1">
                    Optimize <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Products Table + Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Products Table -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500">Recent Products</h3>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">View All →</a>
            </div>
            <div class="data-table-wrapper">
                <table class="data-table" role="table" aria-label="Recent Products">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProducts as $product)
                        <tr data-href="{{ route('products.edit', $product->id) }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $product->name }}</p>
                                        <code class="text-xs text-slate-400 font-mono">{{ $product->sku }}</code>
                                    </div>
                                </div>
                            </td>
                            <td class="font-semibold text-slate-700 dark:text-slate-300">Rs. {{ number_format($product->price) }}</td>
                            <td class="text-slate-600 dark:text-slate-400">{{ $product->current_stock }} units</td>
                            <td>
                                @if($product->isLowStock())
                                    <span class="badge-amber">Low Stock</span>
                                @else
                                    <span class="badge-green">In Stock</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-icon" aria-label="Edit {{ $product->name }}" data-tooltip="Edit product">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-slate-400 text-sm">No products found.</p>
                                <a href="{{ route('products.create') }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">Add your first product</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Activity -->
        <div class="card p-6 flex flex-col">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-5">System Activity</h3>

            @if(count($recentActivity) > 0)
                <div class="relative pl-5 border-l-2 border-slate-100 dark:border-slate-700 space-y-5 flex-1">
                    @foreach($recentActivity as $activity)
                    <div class="relative">
                        <div class="absolute -left-[1.35rem] top-1 w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 flex items-center justify-center
                            @if($activity['color'] === 'red') bg-red-100 dark:bg-red-900/30 ring-1 ring-red-400
                            @elseif($activity['color'] === 'orange') bg-orange-100 dark:bg-orange-900/30 ring-1 ring-orange-400
                            @elseif($activity['color'] === 'amber') bg-amber-100 dark:bg-amber-900/30 ring-1 ring-amber-400
                            @elseif($activity['color'] === 'green') bg-green-100 dark:bg-green-900/30 ring-1 ring-green-400
                            @else bg-blue-100 dark:bg-blue-900/30 ring-1 ring-blue-400
                            @endif">
                            <div class="w-1.5 h-1.5 rounded-full
                                @if($activity['color'] === 'red') bg-red-500
                                @elseif($activity['color'] === 'orange') bg-orange-500
                                @elseif($activity['color'] === 'amber') bg-amber-500
                                @elseif($activity['color'] === 'green') bg-green-500
                                @else bg-blue-500
                                @endif"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $activity['title'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $activity['body'] }}</p>
                            <span class="text-xs text-slate-400 mt-1 block">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8 text-slate-400">
                    <svg class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">No recent activity yet.</p>
                </div>
            @endif

            <a href="{{ route('analytics.alerts') }}"
               class="mt-4 w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 transition-colors block text-center">
                View All Alerts →
            </a>
        </div>
    </div>
</div>
