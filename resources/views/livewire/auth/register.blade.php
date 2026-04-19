<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full bg-white p-8 rounded-2xl shadow-2xl border border-gray-200">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl items-center justify-center shadow-xl mb-4 hover:scale-105 transition-transform duration-300">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Create Admin Account
            </h2>
            <p class="text-base text-gray-600 font-medium">
                Already have an account? <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline underline-offset-2 transition-all">Sign in</a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" wire:submit.prevent="register">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-800 mb-2.5">Full Name</label>
                    <input wire:model="name" id="name" type="text" required 
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="John Doe">
                    @error('name') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>
                
                <div>
                    <label for="company_name" class="block text-sm font-bold text-gray-800 mb-2.5">Company Name</label>
                    <input wire:model="company_name" id="company_name" type="text" required 
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="Rehman Engineering Works">
                    @error('company_name') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-800 mb-2.5">Email Address</label>
                    <input wire:model="email" id="email" type="email" required 
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="admin@example.com">
                    @error('email') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-800 mb-2.5">Password</label>
                    <input wire:model="password" id="password" type="password" required 
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="••••••••">
                    @error('password') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-800 mb-2.5">Confirm Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required 
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 active:scale-[0.98] text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                    </svg>
                    <span>Create Admin Account</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
