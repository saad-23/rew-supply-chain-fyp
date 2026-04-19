<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">System Alerts & Monitoring</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Stay informed about critical business events</p>
        </div>
        
        <button wire:click="refreshAlerts" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition-all">
            <svg wire:loading.remove wire:target="refreshAlerts" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg wire:loading wire:target="refreshAlerts" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Refresh</span>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium uppercase tracking-wide">Critical Alerts</p>
                    <p class="text-4xl font-bold mt-2">{{ $criticalCount }}</p>
                    <p class="text-red-100 text-sm mt-1">Requires immediate attention</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">High Priority</p>
                    <p class="text-4xl font-bold mt-2">{{ $highCount }}</p>
                    <p class="text-orange-100 text-sm mt-1">Action needed soon</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Active</p>
                    <p class="text-4xl font-bold mt-2">{{ $totalUnresolved }}</p>
                    <p class="text-blue-100 text-sm mt-1">Unresolved issues</p>
                </div>
                <div class="p-4 rounded-full bg-white bg-opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
            <div class="flex items-center gap-3">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filter by Severity:</label>
                <select wire:model.live="filter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-4">
                    <option value="all">All Alerts</option>
                    <option value="critical">Critical Only</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.live="showResolved" id="showResolved" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <label for="showResolved" class="text-sm text-gray-700 dark:text-gray-300">Show resolved alerts</label>
            </div>
        </div>
    </div>

    <!-- Alerts List -->
    <div class="space-y-4">
        @forelse($alerts as $alert)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 
                {{ $alert->severity === 'critical' ? 'border-red-500' : '' }}
                {{ $alert->severity === 'high' ? 'border-orange-500' : '' }}
                {{ $alert->severity === 'medium' ? 'border-yellow-500' : '' }}
                {{ $alert->severity === 'low' ? 'border-blue-500' : '' }}
                {{ $alert->is_resolved ? 'opacity-60' : '' }}">
                
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <!-- Icon -->
                        <div class="p-3 rounded-lg flex-shrink-0
                            {{ $alert->severity === 'critical' ? 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300' : '' }}
                            {{ $alert->severity === 'high' ? 'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300' : '' }}
                            {{ $alert->severity === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300' : '' }}
                            {{ $alert->severity === 'low' ? 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}">
                            @if($alert->type === 'low_stock')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @elseif($alert->type === 'delivery_delay')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase
                                    {{ $alert->severity === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $alert->severity === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $alert->severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $alert->severity === 'low' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $alert->severity }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $alert->detected_at->diffForHumans() }}</span>
                                @if($alert->is_resolved)
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-800">RESOLVED</span>
                                @endif
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $alert->description }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 ml-4">
                        @if(!$alert->is_resolved)
                            <button wire:click="resolveAlert({{ $alert->id }})" 
                                    class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium transition-all">
                                Resolve
                            </button>
                        @endif
                        <button wire:click="deleteAlert({{ $alert->id }})" 
                                wire:confirm="Are you sure you want to delete this alert?"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-all">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">No alerts found</p>
                <p class="text-sm text-gray-500 mt-2">Your system is running smoothly!</p>
            </div>
        @endforelse
    </div>
</div>
