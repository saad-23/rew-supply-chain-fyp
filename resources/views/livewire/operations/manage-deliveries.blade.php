<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Manage Deliveries</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track and manage all product deliveries</p>
        </div>
        
        <a href="{{ route('operations.create-delivery') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New Delivery</span>
        </a>
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $statistics['total'] }}</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg shadow-sm p-4">
            <div class="text-xs text-yellow-700 dark:text-yellow-300 uppercase tracking-wide">Pending</div>
            <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-200 mt-1">{{ $statistics['pending'] }}</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg shadow-sm p-4">
            <div class="text-xs text-blue-700 dark:text-blue-300 uppercase tracking-wide">In Transit</div>
            <div class="text-2xl font-bold text-blue-800 dark:text-blue-200 mt-1">{{ $statistics['in_transit'] }}</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900 rounded-lg shadow-sm p-4">
            <div class="text-xs text-green-700 dark:text-green-300 uppercase tracking-wide">Delivered</div>
            <div class="text-2xl font-bold text-green-800 dark:text-green-200 mt-1">{{ $statistics['delivered'] }}</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900 rounded-lg shadow-sm p-4">
            <div class="text-xs text-red-700 dark:text-red-300 uppercase tracking-wide">Failed</div>
            <div class="text-2xl font-bold text-red-800 dark:text-red-200 mt-1">{{ $statistics['failed'] }}</div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900 rounded-lg shadow-sm p-4">
            <div class="text-xs text-orange-700 dark:text-orange-300 uppercase tracking-wide">High Priority</div>
            <div class="text-2xl font-bold text-orange-800 dark:text-orange-200 mt-1">{{ $statistics['high_priority'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search by customer, address, or product..." 
                       class="w-full py-2 px-4 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-4">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filterPriority" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-4">
                    <option value="all">All Priority</option>
                    <option value="1">Normal</option>
                    <option value="2">High Priority</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $delivery->customer_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($delivery->product)
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $delivery->product->name }}</div>
                                    <div class="text-xs text-gray-500">Qty: {{ $delivery->quantity }}</div>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $delivery->address }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $delivery->delivery_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($delivery->priority == 2)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">High</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Normal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($delivery->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                    @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button wire:click="editStatus({{ $delivery->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                    Edit
                                </button>
                                <button wire:click="deleteDelivery({{ $delivery->id }})" 
                                        wire:confirm="Are you sure you want to delete this delivery?"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No deliveries found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $deliveries->links() }}
        </div>
    </div>

    <!-- Edit Status Modal -->
    @if($editingId)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelEdit">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Update Delivery Status</h3>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select wire:model="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="pending">Pending</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                        <option value="failed">Failed</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Add any notes..."></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3">
                    <button wire:click="updateStatus" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Update
                    </button>
                    <button wire:click="cancelEdit" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
