<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Manage your account settings and password</p>
        </div>
    </div>

    <div class="max-w-3xl card card-body">
        @if (session()->has('message'))
            <div class="alert-success mb-6">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <form wire:submit="updateProfile">
            <div class="grid grid-cols-1 gap-6">
                <!-- Avatar Section (Visual only for now) -->
                <div class="flex items-center gap-5">
                    <div class="h-20 w-20 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 text-3xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ $name }}</h3>
                        <span class="badge-blue mt-1">Administrator</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="form-label">Full Name</label>
                        <input type="text" wire:model="name" class="input-enhanced @error('name') error @enderror" placeholder="Full name" aria-label="Full name">
                        @error('name') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" wire:model="email" class="input-enhanced @error('email') error @enderror" placeholder="Email address" aria-label="Email address">
                        @error('email') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-2">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">Change Password</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">New Password</label>
                            <input type="password" wire:model="password" class="input-enhanced @error('password') error @enderror" placeholder="Leave blank to keep current" aria-label="New password">
                            @error('password') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Confirm Password</label>
                            <input type="password" wire:model="password_confirmation" class="input-enhanced" placeholder="Retype new password" aria-label="Confirm new password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn-primary btn-lg" aria-label="Update profile">
                    <span wire:loading.remove wire:target="updateProfile">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Profile
                    </span>
                    <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                        <span class="btn-spinner"></span> Saving…
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
