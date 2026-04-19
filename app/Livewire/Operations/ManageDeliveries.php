<?php

namespace App\Livewire\Operations;

use App\Models\Delivery;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
class ManageDeliveries extends Component
{
    use WithPagination;

    public $filterStatus = 'all';
    public $filterPriority = 'all';
    public $search = '';
    public $editingId = null;
    public $status;
    public $notes;

    protected $queryString = ['filterStatus', 'filterPriority', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPriority()
    {
        $this->resetPage();
    }

    public function editStatus($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        $this->editingId = $deliveryId;
        $this->status = $delivery->status;
        $this->notes = $delivery->notes;
    }

    public function updateStatus()
    {
        $this->validate([
            'status' => 'required|in:pending,in_transit,delivered,failed',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $delivery = Delivery::findOrFail($this->editingId);
            $delivery->update([
                'status' => $this->status,
                'notes' => $this->notes
            ]);

            $this->editingId = null;
            session()->flash('message', 'Delivery status updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating delivery status: ' . $e->getMessage());
            session()->flash('error', 'Failed to update delivery status');
        }
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->status = null;
        $this->notes = null;
    }

    public function deleteDelivery($id)
    {
        try {
            Delivery::destroy($id);
            session()->flash('message', 'Delivery deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting delivery: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete delivery');
        }
    }

    public function render()
    {
        $query = Delivery::with('product');

        // Apply filters
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority !== 'all') {
            $query->where('priority', $this->filterPriority);
        }

        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($productQuery) {
                      $productQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $deliveries = $query->orderBy('delivery_date', 'desc')
                           ->orderBy('priority', 'desc')
                           ->paginate(15);

        return view('livewire.operations.manage-deliveries', [
            'deliveries' => $deliveries,
            'statistics' => $this->getStatistics()
        ]);
    }

    private function getStatistics()
    {
        return [
            'total' => Delivery::count(),
            'pending' => Delivery::where('status', 'pending')->count(),
            'in_transit' => Delivery::where('status', 'in_transit')->count(),
            'delivered' => Delivery::where('status', 'delivered')->count(),
            'failed' => Delivery::where('status', 'failed')->count(),
            'high_priority' => Delivery::where('priority', 2)->where('status', '!=', 'delivered')->count(),
        ];
    }
}
