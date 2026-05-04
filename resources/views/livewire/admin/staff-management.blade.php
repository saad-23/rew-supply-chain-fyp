<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Staff Management</h1>
            <p class="page-subtitle">Create and manage staff accounts</p>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Create Staff Form -->
        <div class="card card-body h-fit">
            <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">Add New Staff Member</h2>

            <form wire:submit="createStaff" novalidate class="space-y-4">
                <div>
                    <label class="form-label">Full Name</label>
                    <input wire:model="name" type="text" class="input-enhanced @error('name') error @enderror" placeholder="e.g. Ali Raza" autocomplete="off">
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Email Address</label>
                    <input wire:model="email" type="email" class="input-enhanced @error('email') error @enderror" placeholder="staff@company.com" autocomplete="off">
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Password</label>
                    <input wire:model="password" type="password" class="input-enhanced @error('password') error @enderror" placeholder="Min 8 characters">
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full">
                    <span wire:loading.remove wire:target="createStaff" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Staff Account
                    </span>
                    <span wire:loading wire:target="createStaff" class="flex items-center justify-center gap-2">
                        <span class="btn-spinner"></span> Creating…
                    </span>
                </button>
            </form>
        </div>

        <!-- Staff List -->
        <div class="lg:col-span-2">
            <!-- Search -->
            <div class="mb-4">
                <input wire:model.live.debounce.300ms="search"
                       type="search"
                       class="input-enhanced"
                       placeholder="Search staff by name or email…"
                       aria-label="Search staff">
            </div>

            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors" wire:key="staff-{{ $member->id }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-slate-200 text-sm">{{ $member->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($member->is_active)
                                        <span class="badge-green">Active</span>
                                    @else
                                        <span class="badge-red">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button wire:click="editStaff({{ $member->id }})"
                                            class="btn-icon text-blue-600 hover:bg-blue-50"
                                            data-tooltip="Edit staff">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteStaff({{ $member->id }})"
                                            wire:confirm="Remove {{ $member->name }} from staff? This cannot be undone."
                                            class="btn-icon text-red-500 hover:bg-red-50"
                                            data-tooltip="Delete staff">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-sm">No staff members yet. Create one using the form.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    @if($editingId)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center" wire:click="cancelEdit">
            <div class="w-96 rounded-2xl bg-white dark:bg-slate-800 shadow-2xl p-6 border border-slate-200 dark:border-slate-700" wire:click.stop>
                <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-5">Edit Staff Member</h3>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Full Name</label>
                        <input wire:model="editName" type="text" class="input-enhanced @error('editName') error @enderror">
                        @error('editName') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input wire:model="editEmail" type="email" class="input-enhanced @error('editEmail') error @enderror">
                        @error('editEmail') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-3">
                        <input wire:model="editIsActive" type="checkbox" id="edit_is_active"
                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="edit_is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">Account Active</label>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="updateStaff" class="btn-primary flex-1">
                        <span wire:loading.remove wire:target="updateStaff">Save Changes</span>
                        <span wire:loading wire:target="updateStaff" class="flex items-center gap-2"><span class="btn-spinner"></span> Saving…</span>
                    </button>
                    <button wire:click="cancelEdit" class="btn-secondary flex-1">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
