<x-layouts.admin title="Admin Dashboard">
    <div class="space-y-6">
        
        <!-- Welcome Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Overview of your system performance and activities.</p>
            </div>
            <div class="flex items-center gap-3">
                 
                
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Products</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Product::count() }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 text-indigo-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium flex items-center bg-green-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        +12.5%
                    </span>
                    <span class="text-gray-400 ml-2">vs last month</span>
                </div>
            </div>

            <!-- Stock Value -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Stock Value</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2 tracking-tight">
                            {{ number_format(\App\Models\Product::sum(\DB::raw('current_stock * price')), 0) }}
                            <span class="text-lg font-normal text-gray-400">PKR</span>
                        </h3>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 text-emerald-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium flex items-center bg-green-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        +8.2%
                    </span>
                    <span class="text-gray-400 ml-2">vs last month</span>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Low Stock Alerts</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">5</h3>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl group-hover:bg-amber-100 text-amber-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <a href="#" class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center group-hover:underline">
                        View Low Stock Items <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Active Deliveries -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Deliveries</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Delivery::where('status', 'pending')->count() }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 text-blue-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <a href="{{ route('logistics.routes') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center group-hover:underline">
                        Optimize Routes <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
                    <span class="text-gray-400 ml-2">new this week</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Products Table -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Recent Products</h3>
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 font-medium">Product</th>
                                <th class="px-6 py-3 font-medium">Price</th>
                                <th class="px-6 py-3 font-medium">Stock</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse(\App\Models\Product::latest()->take(5)->get() as $product)
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">ID: #{{ $product->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">Rs. {{ number_format($product->price) }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $product->current_stock }} units</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->current_stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $product->current_stock > 10 ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                        {{ $product->current_stock > 10 ? 'In Stock' : 'Low Stock' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>No products found in the database.</p>
                                        <button class="mt-2 text-indigo-600 hover:underline">Add your first product</button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notifications / Activity -->
             <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                 <h3 class="text-lg font-bold text-gray-900 mb-4">System Activity</h3>
                <div class="relative pl-4 border-l-2 border-gray-100 space-y-8 my-5">
                    
                    <div class="relative">
                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-blue-100 border-2 border-white ring-1 ring-blue-500 flex items-center justify-center">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                        </div>
                         <div>
                             <p class="text-sm font-semibold text-gray-900">System Update</p>
                             <p class="text-xs text-gray-500 mt-1">Successfully updated to version 2.4.0 with new security patches.</p>
                             <span class="text-xs text-gray-400 mt-1 block">2 hours ago</span>
                         </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-amber-100 border-2 border-white ring-1 ring-amber-500 flex items-center justify-center">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                        </div>
                         <div>
                             <p class="text-sm font-semibold text-gray-900">Storage Warning</p>
                             <p class="text-xs text-gray-500 mt-1">Disk usage is above 85%. Consider cleaning up old logs.</p>
                             <span class="text-xs text-gray-400 mt-1 block">5 hours ago</span>
                         </div>
                    </div>
                     <div class="relative">
                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-green-100 border-2 border-white ring-1 ring-green-500 flex items-center justify-center">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                        </div>
                         <div>
                             <p class="text-sm font-semibold text-gray-900">Backup Completed</p>
                             <p class="text-xs text-gray-500 mt-1">Daily database backup completed successfully (45MB).</p>
                             <span class="text-xs text-gray-400 mt-1 block">1 day ago</span>
                         </div>
                    </div>
                </div>
                <button class="w-full py-2 text-sm text-gray-500 hover:text-indigo-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    View All Activity
                </button>
            </div>
        </div>
    </div>
</x-layouts.admin>
