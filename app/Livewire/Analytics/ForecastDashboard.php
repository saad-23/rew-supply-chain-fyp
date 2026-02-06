<?php

namespace App\Livewire\Analytics;

use App\Models\DemandForecast;
use App\Models\Product;
use App\Services\ForecastingService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class ForecastDashboard extends Component
{
    public $selectedProductId;
    public $forecastData = [];
    public $labels = [];
    
    public function mount(ForecastingService $service)
    {
        // Auto-generate some data for demo if empty
        if (DemandForecast::count() == 0) {
            $service->generateForecasts();
        }
        
        $this->selectedProductId = Product::first()->id ?? 0;
        $this->loadData();
    }
    
    public function updatedSelectedProductId()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        if (!$this->selectedProductId) return;
        
        $data = DemandForecast::where('product_id', $this->selectedProductId)
            ->where('forecast_date', '>=', now())
            ->orderBy('forecast_date')
            ->take(14) // Next 2 weeks
            ->get();
            
        $this->labels = $data->pluck('forecast_date')->map(fn($d) => $d->format('M d'))->toArray();
        $this->forecastData = $data->pluck('predicted_quantity')->toArray();
        
        // Dispatch event for Chart.js update
        $this->dispatch('update-chart', labels: $this->labels, data: $this->forecastData);
    }

    public function downloadReport()
    {
        $data = DemandForecast::where('product_id', $this->selectedProductId)
            ->where('forecast_date', '>=', now())
            ->orderBy('forecast_date')
            ->get();
            
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Predicted Quantity', 'Confidence Score']);
            
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->forecast_date->format('Y-m-d'),
                    $row->predicted_quantity,
                    $row->confidence_score
                ]);
            }
            
            fclose($handle);
        }, 'forecast-report-' . now()->format('Y-m-d') . '.csv');
    }
    
    public function render()
    {
        return view('livewire.analytics.forecast-dashboard', [
            'products' => Product::all(),
            'optimization' => $this->selectedProductId ? (new ForecastingService)->optimizeInventory(Product::find($this->selectedProductId)) : null
        ]);
    }
}
