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

    // Reactive fields for UX preview
    public float $selectedProductPrice = 0;
    public int $selectedProductStock  = 0;
    public float $estimatedTotal      = 0;

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d');
        $first = Product::first();
        if ($first) {
            $this->product_id           = $first->id;
            $this->selectedProductPrice = (float) $first->price;
            $this->selectedProductStock = (int) $first->current_stock;
        }
    }

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|integer|min:1',
        'sale_date'  => 'required|date',
    ];

    /**
     * Reactive: update price/stock preview when product changes.
     */
    public function updatedProductId($value): void
    {
        $product = Product::find($value);
        if ($product) {
            $this->selectedProductPrice = (float) $product->price;
            $this->selectedProductStock = (int) $product->current_stock;
        } else {
            $this->selectedProductPrice = 0;
            $this->selectedProductStock = 0;
        }
        $this->recalcTotal();
    }

    /**
     * Reactive: recalculate estimated total when quantity changes.
     */
    public function updatedQuantity(): void
    {
        $this->recalcTotal();
    }

    private function recalcTotal(): void
    {
        $qty = (int) $this->quantity;
        $this->estimatedTotal = ($qty > 0 && $this->selectedProductPrice > 0)
            ? $qty * $this->selectedProductPrice
            : 0;
    }

    public function save()
    {
        $this->validate();

        $product = Product::find($this->product_id);

        if ($product->current_stock < $this->quantity) {
            $this->addError('quantity', 'Insufficient stock. Only ' . $product->current_stock . ' units available.');
            return;
        }

        $product->decrement('current_stock', $this->quantity);

        $sale = Sale::create([
            'product_id'   => $this->product_id,
            'quantity'     => $this->quantity,
            'total_amount' => $product->price * $this->quantity,
            'sale_date'    => $this->sale_date,
        ]);

        $this->lastSaleId     = $sale->id;
        $this->estimatedTotal = 0;

        session()->flash('message', 'Sale recorded and stock updated successfully.');
        $this->reset(['quantity']);

        // Refresh preview after reset
        $this->updatedProductId($this->product_id);
    }

    public function render()
    {
        return view('livewire.operations.record-sale', [
            'products'    => Product::orderBy('name')->get(),
            'recentSales' => Sale::with('product')->latest()->take(5)->get(),
        ]);
    }
}
