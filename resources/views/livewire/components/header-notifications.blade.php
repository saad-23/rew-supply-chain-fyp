<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false" class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($notifications->count() > 0)
        <span class="absolute top-1.5 right-1.5 flex h-3 w-3">
             <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
             <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 text-[8px] font-bold text-white items-center justify-center"></span>
        </span>
        @endif
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
         style="display: none;">
        
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if($notifications->count() > 0)
            <button wire:click="markAllRead" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors relative group">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->data['message'] ?? 'You have a new alert.' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <button wire:click="markAsRead('{{ $notification->id }}')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity" title="Mark as read">
                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No new notifications</p>
            </div>
            @endforelse
        </div>
        
        <div class="bg-gray-50 p-2 text-center border-t border-gray-100">
            <a href="{{ route('analytics.alerts') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium block">View all alerts</a>
        </div>
    </div>
</div>
