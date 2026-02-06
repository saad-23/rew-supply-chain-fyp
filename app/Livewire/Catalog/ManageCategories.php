<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('components.layouts.admin')]
class ManageCategories extends Component
{
    public $name;
    public $desc;
    public $color = 'indigo';
    
    // Sorting
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'name' => 'required|string|max:255',
        'color' => 'required|string',
    ];

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->validate();
        
        Category::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'description' => $this->desc,
            'color' => $this->color
        ]);

        $this->reset(['name', 'desc', 'color']);
        session()->flash('message', 'Category created successfully.');
    }

    #[On('delete-confirmed')]
    public function delete($id)
    {
        Category::find($id)->delete();
        $this->dispatch('category-deleted', message: 'Category deleted successfully');
    }

    public function render()
    {
        return view('livewire.catalog.manage-categories', [
            'categories' => Category::withCount('products')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->get()
        ]);
    }
}
