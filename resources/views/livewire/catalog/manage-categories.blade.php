<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Product Categories</h1>
            <p class="text-sm text-gray-500">Organize your inventory</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Create Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm h-fit">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Add New Category</h3>
            <form wire:submit="create">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" wire:model="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Home Appliances">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Tag</label>
                     <select wire:model="color" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea wire:model="desc" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Add Category
                </button>
            </form>
        </div>

        <!-- List -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
             <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('name')">
                            Name
                            @if ($sortBy === 'name') <span class="ml-1 text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('products_count')">
                            Products
                            @if ($sortBy === 'products_count') <span class="ml-1 text-indigo-500">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories as $cat)
                    <tr>
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
                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors">
                                Delete
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
