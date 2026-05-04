<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Deliveries</h1>
            <p class="page-subtitle">Track and manage all product deliveries</p>
        </div>
        <a href="{{ route('operations.create-delivery') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Delivery
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert-success mb-5">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert-error mb-5">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="kpi-card">
            <div class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Total</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $statistics['total'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="text-xs text-amber-600 uppercase tracking-wide font-semibold">Pending</div>
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-400 mt-1">{{ $statistics['pending'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="text-xs text-blue-600 uppercase tracking-wide font-semibold">In Transit</div>
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-400 mt-1">{{ $statistics['in_transit'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="text-xs text-emerald-600 uppercase tracking-wide font-semibold">Delivered</div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-1">{{ $statistics['delivered'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="text-xs text-red-600 uppercase tracking-wide font-semibold">Failed</div>
            <div class="text-2xl font-bold text-red-700 dark:text-red-400 mt-1">{{ $statistics['failed'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="text-xs text-orange-600 uppercase tracking-wide font-semibold">High Priority</div>
            <div class="text-2xl font-bold text-orange-700 dark:text-orange-400 mt-1">{{ $statistics['high_priority'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-body mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by customer, address, or product..."
                       class="input-enhanced"
                       aria-label="Search deliveries">
            </div>
            <div>
                <select wire:model.live="filterStatus" class="select-enhanced" aria-label="Filter by status">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filterPriority" class="select-enhanced" aria-label="Filter by priority">
                    <option value="all">All Priority</option>
                    <option value="1">Normal</option>
                    <option value="2">High Priority</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="data-table-wrapper">
        <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td>
                                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $delivery->customer_name }}</div>
                            </td>
                            <td>
                                @if($delivery->product)
                                    <div class="text-sm text-slate-800 dark:text-slate-200">{{ $delivery->product->name }}</div>
                                    <div class="text-xs text-slate-400">Qty: {{ $delivery->quantity }}</div>
                                @else
                                    <span class="text-xs text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">{{ $delivery->address }}</div>
                            </td>
                            <td>
                                <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $delivery->delivery_date->format('M d, Y') }}</div>
                            </td>
                            <td>
                                @if($delivery->priority == 2)
                                    <span class="badge-red">High</span>
                                @else
                                    <span class="badge-slate">Normal</span>
                                @endif
                            </td>
                            <td>
                                <span class="
                                    @if($delivery->status === 'pending') badge-amber
                                    @elseif($delivery->status === 'in_transit') badge-blue
                                    @elseif($delivery->status === 'delivered') badge-green
                                    @else badge-red
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button wire:click="editStatus({{ $delivery->id }})" class="btn-icon text-blue-600 hover:bg-blue-50" data-tooltip="Edit status" aria-label="Edit delivery status">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteDelivery({{ $delivery->id }})"
                                        wire:confirm="Are you sure you want to delete this delivery?"
                                        class="btn-icon text-red-500 hover:bg-red-50" data-tooltip="Delete delivery" aria-label="Delete delivery">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm">No deliveries found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $deliveries->links() }}
        </div>
    </div>

    <!-- Edit Status Modal -->
    @if($editingId)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center" wire:click="cancelEdit">
            <div class="relative p-6 border border-slate-200 dark:border-slate-700 w-96 shadow-2xl rounded-2xl bg-white dark:bg-slate-800" wire:click.stop>
                <div class="mb-5">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600">Update Delivery Status</h3>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select wire:model="status" class="select-enhanced" aria-label="Delivery status">
                        <option value="pending">Pending</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                        <option value="failed">Failed</option>
                    </select>
                    @error('status') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="form-label">Notes <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <textarea wire:model="notes" rows="3" class="input-enhanced" placeholder="Add any notes..."></textarea>
                    @error('notes') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button wire:click="updateStatus" class="btn-primary flex-1">
                        <span wire:loading.remove wire:target="updateStatus">Update</span>
                        <span wire:loading wire:target="updateStatus" class="flex items-center gap-2"><span class="btn-spinner"></span> Saving…</span>
                    </button>
                    </button>
                    <button wire:click="cancelEdit" class="btn-secondary flex-1">Cancel</button> bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
