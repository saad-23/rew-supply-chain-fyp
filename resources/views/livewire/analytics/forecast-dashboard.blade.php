<div class="p-6">
    <!-- Success/Error Messages -->
    @if($successMessage)
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Demand Forecast & Stock Planning</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Plan your inventory for the next 3 months based on historical sales data</p>
    </div>

    <!-- Product Selection & Actions -->
    <div class="card card-body mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="flex-1">
                <label for="forecast-product" class="form-label">Select Product</label>
                <select id="forecast-product" wire:model.live="selectedProductId"
                        class="select-enhanced" aria-label="Select product for forecast">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @if($salesCount > 0)
                    <p class="text-xs text-slate-400 mt-1.5">Based on <strong class="text-blue-600">{{ $salesCount }}</strong> historical sales records</p>
                @endif
            </div>
            
            <div class="flex gap-3 items-end flex-shrink-0">
                <button wire:click="generateForecast" wire:loading.attr="disabled"
                        class="btn-primary btn-lg" aria-label="Generate demand forecast">
                    <svg wire:loading.remove wire:target="generateForecast" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="btn-spinner" wire:loading wire:target="generateForecast"></span>
                    <span wire:loading.remove wire:target="generateForecast">Generate Forecast</span>
                    <span wire:loading wire:target="generateForecast">Generating…</span>
                </button>
                
                <button wire:click="downloadReport" class="btn-secondary btn-lg" aria-label="Export forecast report">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    {{-- Skeleton while generating --}}
    <div wire:loading wire:target="generateForecast" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @for($i=0;$i<3;$i++)
        <div class="skeleton-card h-36"></div>
        @endfor
    </div>

    <div wire:loading.remove wire:target="generateForecast" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="rounded-2xl shadow-lg p-6 text-white metric-blue">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest"
                       data-tooltip="Total units your data predicts will sell in the next 30 days">Predicted Demand
                        <svg class="inline w-3 h-3 ml-0.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </p>
                    <p class="text-4xl font-bold mt-3">{{ number_format($totalForecast) }}</p>
                    <p class="text-blue-100 text-sm mt-2">Units needed in next 30 days</p>
                </div>
                <div class="p-4 rounded-full bg-white/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg p-6 text-white metric-green">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-green-100 text-xs font-bold uppercase tracking-widest"
                       data-tooltip="Average number of units expected to be sold per calendar day">Daily Average
                        <svg class="inline w-3 h-3 ml-0.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </p>
                    <p class="text-4xl font-bold mt-3">{{ number_format($avgDaily, 1) }}</p>
                    <p class="text-green-100 text-sm mt-2">Units expected per day</p>
                </div>
                <div class="p-4 rounded-full bg-white/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg p-6 text-white metric-orange">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-orange-100 text-xs font-bold uppercase tracking-widest">Current Stock</p>
                    <p class="text-4xl font-bold mt-3">{{ $product ? number_format($product->current_stock) : 0 }}</p>
                    <p class="text-orange-100 text-sm mt-2">Units available now</p>
                </div>
                <div class="p-4 rounded-full bg-white/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Planning Recommendations -->
    @if($optimization)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 mb-6 border-2 border-blue-100 dark:border-blue-900">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-0.5">Inventory Action Plan</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Smart recommendations to optimize your stock levels</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-5 border-l-4 border-red-500">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400"
                            data-tooltip="Order new stock when inventory reaches this level to avoid stockouts">Reorder Point</h3>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($optimization['reorder_point']) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Order when stock hits this level</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-5 border-l-4 border-emerald-500">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400"
                            data-tooltip="Safety Stock: buffer units kept on hand to absorb unexpected demand spikes">Safety Stock</h3>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($optimization['safety_stock']) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Extra buffer for demand spikes</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400"
                            data-tooltip="EOQ (Economic Order Quantity): the optimal batch size that minimizes total ordering and holding costs">EOQ</h3>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($optimization['eoq']) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Optimal units per order</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Chart -->
    <div class="chart-container">
        <div class="mb-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h3 class="chart-title">{{ $periodLabel }} Demand Trend</h3>
                <p class="chart-subtitle">Month-wise predicted demand to help plan procurement</p>
            </div>

            {{-- Duration Picker --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Forecast Range</label>
                <div class="relative">
                    <select wire:model.live="forecastPeriod"
                            class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                   text-slate-700 dark:text-slate-200 text-sm font-medium rounded-xl
                                   pl-3 pr-8 py-2 shadow-sm cursor-pointer
                                   focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                   transition hover:border-indigo-400"
                            aria-label="Select forecast duration">
                        <option value="3months">Next 3 Months</option>
                        <option value="6months">Next 6 Months</option>
                        <option value="12months">Next 12 Months</option>
                        <option value="custom">Custom Days</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Custom Days Input (shown only when custom selected) --}}
                @if($forecastPeriod === 'custom')
                <div class="flex items-center gap-1">
                    <input type="number" wire:model.live.debounce.600ms="customDays"
                           min="7" max="730" placeholder="Days"
                           class="w-20 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                  px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                                  focus:border-indigo-400 transition"
                           aria-label="Number of custom forecast days">
                    <span class="text-xs text-slate-400">days</span>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Skeleton while loading chart --}}
        <div wire:loading wire:target="generateForecast" class="h-80 skeleton rounded-xl"></div>

        <div wire:loading.remove wire:target="generateForecast">
            @if(empty($labels))
            <div class="flex flex-col items-center justify-center h-80 text-slate-300 dark:text-slate-600">
                <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-base font-semibold text-slate-500">No forecast generated yet</p>
                <p class="text-sm text-slate-400 mt-1">Select a product and click "Generate Forecast" above</p>
            </div>
            @else
            <div class="relative h-96 w-full">
                <canvas id="forecastChart"></canvas>
            </div>
            @endif
        </div>
    </div>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endassets

    @script
    <script>
        let chart = null;

        function initChart(labels, data) {
            const ctx = document.getElementById('forecastChart');
            
            if (!ctx) return;
            
            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Demand (Units)',
                        data: data,
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderColor: 'rgb(79, 70, 229)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            padding: 16,
                            cornerRadius: 10,
                            titleFont: { 
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: { 
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Predicted Demand: ' + context.parsed.y.toLocaleString() + ' units';
                                },
                                afterLabel: function(context) {
                                    const daily = Math.round(context.parsed.y / 30);
                                    return [
                                        'Daily Avg: ~' + daily + ' units/day',
                                        'Plan your inventory accordingly.'
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return value.toLocaleString() + ' units';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize on load if data exists
        @if(!empty($labels))
            initChart(@json($labels), @json($forecastData));
        @endif

        // Listen for updates
        $wire.on('update-chart', (event) => {
            if (event && event.length > 0 && event[0].labels && event[0].data) {
                initChart(event[0].labels, event[0].data);
            }
        });
    </script>
    @endscript
</div>
