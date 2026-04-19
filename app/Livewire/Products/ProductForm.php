<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class ProductForm extends Component
{
    public $productId = null;
    public $name = '';
    public $sku = '';
    public $current_stock = '';
    public $price = '';
    public $category_id = '';

    public function mount($productId = null)
    {
        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $this->productId = $product->id;
                $this->name = $product->name;
                $this->sku = $product->sku;
                $this->current_stock = $product->current_stock;
                $this->price = $product->price;
                $this->category_id = $product->category_id;
            }
        }
    }

    public function rules()
    {
        $skuRule = 'required|string|max:255|unique:products,sku';
        if ($this->productId) {
            $skuRule .= ',' . $this->productId;
        }

        return [
            'name' => 'required|string|max:255',
            'sku' => $skuRule,
            'current_stock' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function saveProduct()
    {
        $this->validate();

        if ($this->productId) {
            $product = Product::find($this->productId);
            $product->update([
                'name' => $this->name,
                'sku' => $this->sku,
                'current_stock' => $this->current_stock,
                'price' => $this->price,
                'category_id' => $this->category_id,
            ]);
            $message = 'Product updated successfully!';
        } else {
            Product::create([
                'name' => $this->name,
                'sku' => $this->sku,
                'current_stock' => $this->current_stock,
                'price' => $this->price,
                'category_id' => $this->category_id,
            ]);
            $message = 'Product created successfully!';
        }

        $this->dispatch('product-saved', message: $message);
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'recentProducts' => Product::latest()->take(5)->get(),
            'categories' => Category::orderBy('name')->get()
        ]);
    }
}
