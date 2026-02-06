<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('components.layouts.admin')]
class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->sortDirection = 'asc';
    }

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[On('delete-confirmed')]
    public function deleteProduct($id)
    {
        Product::find($id)->delete();
        $this->dispatch('product-deleted', message: 'Product deleted successfully');
    }

    public function render()
    {
        $products = Product::with('category')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.products.product-list', [
            'products' => $products,
        ]);
    }
}
