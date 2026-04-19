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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Select Product
                </label>
                <select wire:model.live="selectedProductId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-3 text-base font-medium">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @if($salesCount > 0)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Based on {{ $salesCount }} historical sales records</p>
                @endif
            </div>
            
            <div class="flex gap-3 items-end">
                <button wire:click="generateForecast" wire:loading.attr="disabled" class="flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-base font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <svg wire:loading.remove wire:target="generateForecast" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <svg wire:loading wire:target="generateForecast" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="generateForecast">Generate Forecast</span>
                    <span wire:loading wire:target="generateForecast">Generating...</span>
                </button>
                
                <button wire:click="downloadReport" class="flex items-center justify-center px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Predicted Demand</p>
                    <p class="text-4xl font-bold mt-3">{{ number_format($totalForecast) }}</p>
                    <p class="text-blue-100 text-sm mt-2">Units needed in next 30 days</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Daily Average</p>
                    <p class="text-4xl font-bold mt-3">{{ number_format($avgDaily, 1) }}</p>
                    <p class="text-green-100 text-sm mt-2">Units expected per day</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Current Stock</p>
                    <p class="text-4xl font-bold mt-3">{{ $product ? number_format($product->current_stock) : 0 }}</p>
                    <p class="text-orange-100 text-sm mt-2">Units available now</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Planning Recommendations -->
    @if($optimization)
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 mb-6 border-2 border-indigo-200 dark:border-indigo-900">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-1">📦 Inventory Action Plan</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Smart recommendations to optimize your stock levels</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-red-500">
                <div class="flex items-start">
                    <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">When to Reorder</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($optimization['reorder_point']) }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Order new stock when inventory reaches this level</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-green-500">
                <div class="flex items-start">
                    <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Safety Buffer</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($optimization['safety_stock']) }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Extra stock to handle unexpected demand</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                <div class="flex items-start">
                    <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 mr-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Order Quantity</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($optimization['eoq']) }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Optimal units to order each time</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">3-Month Demand Trend</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Month-wise predicted demand to help plan your procurement</p>
        </div>
        
        @if(empty($labels))
            <div class="flex flex-col items-center justify-center h-80 text-gray-400">
                <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-lg font-semibold text-gray-600">No forecast available yet</p>
                <p class="text-sm mt-2">Select a product and click "Generate Forecast" to see predictions</p>
            </div>
        @else
            <div class="relative h-96 w-full">
                <canvas id="forecastChart"></canvas>
            </div>
        @endif
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
                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                            padding: 16,
                            titleFont: { 
                                size: 16,
                                weight: 'bold'
                            },
                            bodyFont: { 
                                size: 14 
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Total Demand: ' + context.parsed.y.toLocaleString() + ' units';
                                },
                                afterLabel: function(context) {
                                    const avgDaily = Math.round(context.parsed.y / 30);
                                    return 'Daily Avg: ' + avgDaily + ' units/day';
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
