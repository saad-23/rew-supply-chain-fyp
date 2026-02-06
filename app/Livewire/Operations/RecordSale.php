<?php

namespace App\Livewire\Operations;

use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class RecordSale extends Component
{
    public $product_id;
    public $quantity;
    public $sale_date;
    public $lastSaleId;
    
    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d');
        $this->product_id = Product::first()?->id;
    }

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'sale_date' => 'required|date',
    ];

    public function save()
    {
        $this->validate();

        $product = Product::find($this->product_id);
        
        // Decrement stock
        if ($product->current_stock >= $this->quantity) {
            $product->decrement('current_stock', $this->quantity);
            
            $sale = Sale::create([
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'total_amount' => $product->price * $this->quantity,
                'sale_date' => $this->sale_date,
            ]);

            $this->lastSaleId = $sale->id;
            session()->flash('message', 'Sale recorded and stock updated successfully.');
            $this->reset(['quantity']);
        } else {
            $this->addError('quantity', 'Insufficient stock available.');
        }
    }

    public function render()
    {
        return view('livewire.operations.record-sale', [
            'products' => Product::all(),
            'recentSales' => Sale::with('product')->latest()->take(5)->get()
        ]);
    }
}
