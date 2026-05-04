<div class="p-6" wire:poll.10s>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Anomaly Detection</h1>
            <p class="page-subtitle">Real-time system monitoring &mdash; auto-refreshes every 10s</p>
        </div>
        <button wire:click="scan" class="btn-danger" aria-label="Run anomaly scan now">
            <span wire:loading.remove wire:target="scan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Scan Now
            </span>
            <span wire:loading wire:target="scan" class="flex items-center gap-2">
                <span class="btn-spinner"></span> Scanning…
            </span>
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-5">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Severity</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Detected At</th>
                    <th>Status</th>
                    <th class="text-right"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anomalies as $anomaly)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td>
                        @if($anomaly->severity == 'critical')
                            <span class="badge-critical">Critical</span>
                        @elseif($anomaly->severity == 'high')
                            <span class="badge-high">High</span>
                        @elseif($anomaly->severity == 'medium')
                            <span class="badge-medium">Medium</span>
                        @else
                            <span class="badge-low">Low</span>
                        @endif
                    </td>
                    <td class="text-sm text-slate-600 dark:text-slate-400">
                        {{ ucfirst(str_replace('_', ' ', $anomaly->type)) }}
                    </td>
                    <td class="text-sm text-slate-800 dark:text-slate-200">
                        {{ $anomaly->description }}
                    </td>
                    <td class="text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                        {{ $anomaly->created_at->diffForHumans() }}
                    </td>
                    <td>
                        @if($anomaly->is_resolved)
                            <span class="badge-green">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Resolved
                            </span>
                        @else
                            <span class="badge-red">Active</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if(!$anomaly->is_resolved)
                            <button wire:click="resolve({{ $anomaly->id }})" class="btn-icon text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50" data-tooltip="Mark as resolved" aria-label="Resolve anomaly">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium text-emerald-600">All clear! System is running smoothly.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-2">
        {{ $anomalies->links() }}
    </div>
</div>
