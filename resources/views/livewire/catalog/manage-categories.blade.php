<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Product Categories</h1>
            <p class="page-subtitle">Organize your inventory into logical groups</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Create Form -->
        <div class="card card-body h-fit">
            <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">Add New Category</h3>
            <form wire:submit="create">
                <div class="mb-4">
                    <label class="form-label">Name</label>
                    <input type="text" wire:model="name" class="input-enhanced @error('name') error @enderror" placeholder="e.g. Home Appliances" aria-label="Category name">
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Color Tag</label>
                    <select wire:model="color" class="select-enhanced" aria-label="Category color">
                        <option value="indigo">Indigo</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                        <option value="blue">Blue</option>
                        <option value="yellow">Yellow</option>
                        <option value="purple">Purple</option>
                        <option value="gray">Gray</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea wire:model="desc" rows="3" class="input-enhanced" aria-label="Category description"></textarea>
                </div>

                <button type="submit" class="btn-primary w-full" aria-label="Add category">
                    <span wire:loading.remove wire:target="create">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Category
                    </span>
                    <span wire:loading wire:target="create" class="flex items-center gap-2">
                        <span class="btn-spinner"></span> Adding…
                    </span>
                </button>
            </form>
        </div>

        <!-- List -->
        <div class="lg:col-span-2 data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="cursor-pointer" wire:click="sortBy('name')">
                            Name
                            @if ($sortBy === 'name') <span class="ml-1 text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="cursor-pointer" wire:click="sortBy('products_count')">
                            Products
                            @if ($sortBy === 'products_count') <span class="ml-1 text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="h-8 w-8 rounded-full bg-{{ $cat->color }}-100 text-{{ $cat->color }}-600 flex items-center justify-center mr-3 font-bold">
                                    {{ substr($cat->name, 0, 1) }}
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $cat->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $cat->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $cat->products_count }} items
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="$dispatch('swal:confirm', { title: 'Delete Category?', text: 'All products in this category will be uncategorized.', type: 'warning', method: 'delete-confirmed', id: {{ $cat->id }} })" 
                                    class="btn-icon text-red-500 hover:text-red-700 hover:bg-red-50" data-tooltip="Delete category" aria-label="Delete {{ $cat->name }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                         <td colspan="3" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
