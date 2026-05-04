<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl items-center justify-center shadow-xl mb-4 hover:scale-105 transition-transform duration-300">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Rehman Engineering Works</h2>
            <p class="text-gray-600 text-base font-medium">Sign in to your admin account</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8">

            <form wire:submit.prevent="login" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-800 mb-2.5">Email Address</label>
                    <input wire:model="email" id="email" type="email" required autocomplete="email"
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="admin@example.com">
                    @error('email') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-800 mb-2.5">Password</label>
                    <input wire:model="password" id="password" type="password" required autocomplete="current-password"
                           class="px-4 w-full py-3.5 text-base rounded-lg border-2 border-gray-300 hover:border-indigo-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 bg-white text-gray-900 font-medium placeholder-gray-500" 
                           placeholder="••••••••">
                    @error('password') <span class="text-red-600 text-sm mt-2 block font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </span> @enderror
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer group">
                        <input wire:model="remember" id="remember_me" type="checkbox" 
                               class="w-4 h-4 text-indigo-600 border-2 border-gray-400 rounded focus:ring-2 focus:ring-indigo-500 cursor-pointer transition-all">
                        <span class="ml-2.5 text-sm text-gray-700 font-medium group-hover:text-indigo-600 transition-colors">Remember me</span>
                    </label>
                    {{-- <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 hover:underline underline-offset-2 transition-all">
                        Forgot password?
                    </a> --}}
                </div>

                <!-- Login Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 active:scale-[0.98] text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300 flex items-center justify-center gap-2 group">
                    <span>Sign in to Dashboard</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>

                {{-- <!-- Register Link -->
                <div class="text-center pt-3">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline underline-offset-2 transition-all">
                            Create account
                        </a>
                    </p>
                </div> --}}
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t-2 border-gray-200 text-center">
                <p class="text-sm text-gray-600 font-medium">© 2026 Rehman Engineering Works. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
