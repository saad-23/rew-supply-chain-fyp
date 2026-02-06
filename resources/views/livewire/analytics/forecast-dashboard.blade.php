<div class="p-6">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Demand Forecasting & Inventory</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">AI-driven predictions for stock management</p>
        </div>
        
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button wire:click="downloadReport" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Report
            </button>
            <div class="w-64">
                <select wire:model.live="selectedProductId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Optimization Cards -->
    @if($optimization)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Reorder Point</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($optimization['reorder_point']) }} <span class="text-sm font-normal text-gray-500">units</span></p>
                    <p class="text-xs text-gray-400 mt-1">Trigger new order when stock hits this level</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-500 dark:text-green-300 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Safety Stock</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($optimization['safety_stock']) }} <span class="text-sm font-normal text-gray-500">units</span></p>
                    <p class="text-xs text-gray-400 mt-1">Buffer stock to prevent stockouts</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-500 dark:text-purple-300 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">EOQ</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($optimization['eoq']) }} <span class="text-sm font-normal text-gray-500">units</span></p>
                    <p class="text-xs text-gray-400 mt-1">Economic Order Quantity</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">14-Day Demand Forecast (LSTM Model)</h3>
        <div class="relative h-80 w-full">
            <canvas id="forecastChart"></canvas>
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
            
            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Predicted Demand',
                        data: data,
                        borderColor: 'rgb(79, 70, 229)', // Indigo 600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(255, 255, 255)',
                        pointBorderColor: 'rgb(79, 70, 229)',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 14 },
                            callbacks: {
                                label: function(context) {
                                    return 'Predicted Sale: ' + context.parsed.y + ' units';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Initialize on load
        initChart(@json($labels), @json($forecastData));

        // Listen for updates
        $wire.on('update-chart', (event) => {
            initChart(event.labels, event.data);
        });
    </script>
    @endscript
</div>
