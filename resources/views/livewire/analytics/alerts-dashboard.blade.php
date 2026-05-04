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
                <div class="p-3 rounded-xl bg-white/20 flex-shrink-0">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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
                <div class="p-3 rounded-xl bg-white/20 flex-shrink-0">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
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
                <div class="p-3 rounded-xl bg-white/20 flex-shrink-0">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-body mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
            <div class="flex items-center gap-3">
                <label for="alert-filter" class="form-label mb-0">Severity:</label>
                <select id="alert-filter" wire:model.live="filter"
                        class="select-enhanced w-auto" aria-label="Filter alerts by severity">
                    <option value="all">All Alerts</option>
                    <option value="critical">Critical Only</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.live="showResolved" id="showResolved"
                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                       aria-label="Show resolved alerts">
                <label for="showResolved" class="text-sm font-medium text-slate-600 dark:text-slate-300 cursor-pointer">Show resolved alerts</label>
            </div>
        </div>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        @forelse($alerts as $alert)
            <div class="card p-5 border-l-4
                {{ $alert->severity === 'critical' ? 'border-red-500' : '' }}
                {{ $alert->severity === 'high'     ? 'border-orange-500' : '' }}
                {{ $alert->severity === 'medium'   ? 'border-amber-400' : '' }}
                {{ $alert->severity === 'low'      ? 'border-blue-400' : '' }}
                {{ $alert->is_resolved ? 'opacity-60' : '' }}
                {{ $alert->severity === 'critical' && !$alert->is_resolved ? 'shadow-red-100 dark:shadow-red-900/20' : '' }}"
                role="article" aria-label="Alert: {{ $alert->severity }} severity">
                
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <!-- Severity Icon -->
                        <div class="p-3 rounded-xl flex-shrink-0
                            {{ $alert->severity === 'critical' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}
                            {{ $alert->severity === 'high'     ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' : '' }}
                            {{ $alert->severity === 'medium'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                            {{ $alert->severity === 'low'      ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}">
                            @if($alert->type === 'low_stock')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @elseif($alert->type === 'delivery_delay')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="badge-{{ $alert->severity }}">{{ strtoupper($alert->severity) }}</span>
                                @if($alert->is_resolved)
                                    <span class="badge-green">RESOLVED</span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $alert->detected_at->diffForHumans() }}</span>
                                @if(isset($alert->anomaly_score) && $alert->anomaly_score)
                                    <span class="text-xs text-slate-400" data-tooltip="Anomaly Score: statistical deviation from expected demand pattern. Higher = more unusual.">Score: {{ number_format($alert->anomaly_score, 2) }}
                                        <svg class="inline w-3 h-3 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                @endif
                            </div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 text-sm">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $alert->description }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$alert->is_resolved)
                            <button wire:click="resolveAlert({{ $alert->id }})" wire:loading.attr="disabled"
                                    class="btn-success btn-sm" aria-label="Resolve alert">
                                <span wire:loading.remove wire:target="resolveAlert({{ $alert->id }})">Resolve</span>
                                <span wire:loading wire:target="resolveAlert({{ $alert->id }})" class="btn-spinner"></span>
                            </button>
                        @endif
                        <button wire:click="deleteAlert({{ $alert->id }})"
                                wire:confirm="Are you sure you want to delete this alert?"
                                class="btn-danger btn-sm" aria-label="Delete alert">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-200 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-base font-semibold text-slate-600 dark:text-slate-400">No alerts found</p>
                <p class="text-sm text-slate-400 mt-1">Your system is running smoothly!</p>
            </div>
        @endforelse
    </div>
</div>
