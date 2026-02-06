<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">My Profile</h1>
            <p class="text-sm text-gray-500">Manage your account settings</p>
        </div>
    </div>

    <div class="max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="updateProfile">
            <div class="grid grid-cols-1 gap-6">
                <!-- Avatar Section (Visual only for now) -->
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-3xl font-bold">
                            {{ substr($name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $name }}</h3>
                        <p class="text-sm text-gray-500">Administrator</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Full Name</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" wire:model="name" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150">
                        </div>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Email Address</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" wire:model="email" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150">
                        </div>
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-2">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Change Password</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">New Password</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" wire:model="password" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="Leave blank to keep current">
                            </div>
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div>
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Confirm Password</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <input type="password" wire:model="password_confirmation" class="pl-10 w-full py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-150" placeholder="Retype new password">
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
