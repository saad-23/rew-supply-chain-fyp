<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    </style>
</head>
<body class="h-full overflow-hidden" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024, 
    mobileSidebarOpen: false,
    toggleSidebar() {
        if (window.innerWidth >= 1024) {
            this.sidebarOpen = !this.sidebarOpen;
        } else {
            this.mobileSidebarOpen = !this.mobileSidebarOpen;
        }
    }
}"
@resize.window="if(window.innerWidth >= 1024) mobileSidebarOpen = false">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"
         @click="mobileSidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out flex flex-col shadow-2xl"
           :class="{ 
               'translate-x-0': mobileSidebarOpen || (sidebarOpen && window.innerWidth >= 1024), 
               '-translate-x-full': !mobileSidebarOpen && (!sidebarOpen || window.innerWidth < 1024),
               'lg:w-20': !sidebarOpen && window.innerWidth >= 1024,
               'lg:translate-x-0': true
           }">
        
        <!-- Logo -->
        <div class="h-16 flex items-center justify-between px-4 bg-slate-950/50 backdrop-blur-sm shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="h-8 w-8 rounded-lg bg-indigo-500 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/20">
                     <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight transition-opacity duration-300 whitespace-nowrap"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">AdminPanel</span>
            </div>
            <!-- Mobile Close Button -->
             <button @click="mobileSidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable Navigation -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-6 px-3 space-y-1">
            
            <!-- Dashboard Link -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Dashboard
                </span>
                <!-- Tooltip for collapsed state -->
                 <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Dashboard
                </div>
            </a>

            <!-- Demand & Inventory -->
            <a href="{{ route('analytics.forecast') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('analytics.forecast') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Forecast & Inventory
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Forecast & Inventory
                </div>
            </a>

            <!-- Route Planner -->
            <a href="{{ route('logistics.routes') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('logistics.routes') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Route Optimization
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Route Optimization
                </div>
            </a>

            <!-- Alerts -->
            <a href="{{ route('analytics.alerts') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('analytics.alerts') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Alerts & Monitor
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Alerts & Monitor
                </div>
            </a>

            <!-- Catalog -->
            <a href="{{ route('catalog.categories') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('catalog.categories') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Catalog
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Catalog
                </div>
            </a>

            <!-- Settings -->
            <a href="{{ route('settings') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('settings') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Settings
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Settings
                </div>
            </a>

            <!-- Profile -->
            <a href="{{ route('profile') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group relative {{ request()->routeIs('profile') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium whitespace-nowrap transition-all duration-300"
                      :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">
                    Profile
                </span>
                <div class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none lg:block hidden z-50 whitespace-nowrap"
                      x-show="!sidebarOpen">
                    Profile
                </div>
            </a>

            <!-- Operations Section -->
            <div x-data="{ open: {{ request()->is('operations*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open; if(!sidebarOpen) toggleSidebar()" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors group">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium whitespace-nowrap transition-all duration-300"
                              :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">Operations</span>
                    </div>
                     <svg class="w-4 h-4 transition-transform duration-200 hidden lg:block" 
                         :class="{ 'rotate-180': open, 'hidden': !sidebarOpen }" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 space-y-1 overflow-hidden"
                     :class="{ 'pl-11': sidebarOpen, 'bg-slate-800/50 rounded-lg py-1 mx-2': !sidebarOpen }">
                    <a href="{{ route('operations.sales') }}" class="block py-2 px-3 rounded-md text-sm {{ request()->routeIs('operations.sales') ? 'text-white bg-indigo-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }} transition-colors">
                        Record Sales
                    </a>
                    <a href="{{ route('operations.delivery') }}" class="block py-2 px-3 rounded-md text-sm {{ request()->routeIs('operations.delivery') ? 'text-white bg-indigo-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }} transition-colors">
                        Create Delivery
                    </a>
                </div>
            </div>

            <!-- Products Section -->
            <div x-data="{ open: {{ request()->is('products*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open; if(!sidebarOpen) toggleSidebar()" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors group">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="font-medium whitespace-nowrap transition-all duration-300"
                              :class="{ 'lg:opacity-0 lg:hidden': !sidebarOpen }">Products</span>
                    </div>
                     <svg class="w-4 h-4 transition-transform duration-200 hidden lg:block" 
                         :class="{ 'rotate-180': open, 'hidden': !sidebarOpen }" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 space-y-1 overflow-hidden"
                     :class="{ 'pl-11': sidebarOpen, 'bg-slate-800/50 rounded-lg py-1 mx-2': !sidebarOpen }">
                    <a href="{{ route('products.index') }}" class="block py-2 px-3 rounded-md text-sm {{ request()->routeIs('products.index') ? 'text-white bg-indigo-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }} transition-colors">
                        Catalog
                    </a>
                    <a href="{{ route('products.create') }}" class="block py-2 px-3 rounded-md text-sm {{ request()->routeIs('products.create') ? 'text-white bg-indigo-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }} transition-colors">
                        Add New
                    </a>
                </div>
            </div>

        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3">
                 <div class="relative">
                     <img class="h-9 w-9 rounded-full object-cover border-2 border-indigo-500" 
                         src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" 
                         alt="User avatar">
                     <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-slate-900"></span>
                 </div>
                <div class="overflow-hidden transition-all duration-300"
                     :class="{ 'lg:opacity-0 lg:w-0': !sidebarOpen }">
                    <p class="text-sm font-medium text-white truncate">Administrator</p>
                    <p class="text-xs text-slate-400 truncate">admin@rewsystem.com</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex flex-col h-screen overflow-hidden transition-all duration-300"
         :class="{ 'lg:ml-72': sidebarOpen, 'lg:ml-20': !sidebarOpen }">
        
        <!-- Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar()" class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                
                <!-- Search -->
                <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white transition-all w-64 lg:w-96">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search products, orders..." class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 w-full ml-2 placeholder-gray-400">
                </div>
            </div>

            <div class="flex items-center gap-4">
                @livewire('components.header-notifications')
                <div class="h-8 w-px bg-gray-200"></div>
                 <div class="relative" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-2 focus:outline-none">
                         <img class="h-8 w-8 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" alt="User">
                         <span class="text-sm font-medium text-gray-700 hidden sm:block">Admin</span>
                         <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- User Dropdown -->
                    <div x-show="userMenu" @click.away="userMenu = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-50 origin-top-right font-light">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Your Profile</a>
                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Settings</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
             @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-md shadow-sm flex items-start justify-between">
                    <div class="flex items-center gap-3">
                         <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('product-deleted', (event) => {
                const message = event.message || 'Product deleted successfully';
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });

            Livewire.on('swal:modal', (data) => {
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.type, // 'success', 'error', 'warning', 'info'
                    confirmButtonText: 'OK'
                });
            });

            Livewire.on('swal:confirm', (data) => {
                 Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: data.confirmButtonText || 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                       Livewire.dispatch(data.method, { id: data.id });
                    }
                });
            });
        });
    </script>

    @livewireScripts
</body>
</html>
