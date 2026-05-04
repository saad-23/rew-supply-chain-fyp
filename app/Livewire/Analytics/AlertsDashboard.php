<?php

namespace App\Livewire\Analytics;

use App\Models\Anomaly;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\DemandForecast;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
class AlertsDashboard extends Component
{
    public $alerts = [];
    public $filter = 'all'; // all, critical, high, medium, low
    public $showResolved = false;

    public function mount()
    {
        $this->generateSystemAlerts();
        $this->loadAlerts();
    }

    public function updatedFilter()
    {
        $this->loadAlerts();
    }

    public function updatedShowResolved()
    {
        $this->loadAlerts();
    }

    /**
     * Auto-generate system alerts based on business rules
     */
    public function generateSystemAlerts()
    {
        try {
            // 1. Low Stock Alerts — uses per-product threshold (not price/10)
            $lowStockProducts = Product::whereColumn('current_stock', '<=', 'low_stock_threshold')->get();

            foreach ($lowStockProducts as $product) {
                $forecast = DemandForecast::where('product_id', $product->id)
                    ->where('forecast_date', '>=', now())
                    ->where('forecast_date', '<=', now()->addDays(7))
                    ->sum('predicted_quantity');

                // Severity based on stock vs forecasted 7-day demand
                if ($product->current_stock == 0) {
                    $severity = 'critical';
                } elseif ($forecast > 0 && $product->current_stock < $forecast) {
                    $severity = 'critical';
                } elseif ($forecast > 0 && $product->current_stock < ($forecast * 1.5)) {
                    $severity = 'high';
                } else {
                    $severity = 'medium';
                }

                Anomaly::firstOrCreate(
                    [
                        'type'        => 'low_stock',
                        'description' => "Low stock alert for {$product->name}. Current: {$product->current_stock} units (threshold: {$product->low_stock_threshold}), 7-day demand: " . round($forecast) . " units",
                        'is_resolved' => false,
                    ],
                    [
                        'severity'    => $severity,
                        'detected_at' => now(),
                    ]
                );
            }

            // 2. Delayed Delivery Alerts
            $delayedDeliveries = Delivery::where('status', 'pending')
                ->where('delivery_date', '<', now())
                ->get();

            foreach ($delayedDeliveries as $delivery) {
                $daysDelayed = now()->diffInDays($delivery->delivery_date);
                $severity = $daysDelayed > 3 ? 'critical' : ($daysDelayed > 1 ? 'high' : 'medium');

                Anomaly::firstOrCreate(
                    [
                        'type' => 'delivery_delay',
                        'description' => "Delivery #{$delivery->id} to {$delivery->customer_name} is {$daysDelayed} day(s) overdue",
                        'is_resolved' => false
                    ],
                    [
                        'severity' => $severity,
                        'detected_at' => now()
                    ]
                );
            }

            // 3. High Demand Surge
            $recentSales = Product::withCount(['sales' => function($q) {
                $q->where('sale_date', '>=', now()->subDays(7));
            }])->having('sales_count', '>', 20)->get();

            foreach ($recentSales as $product) {
                Anomaly::firstOrCreate(
                    [
                        'type' => 'demand_surge',
                        'description' => "High demand detected for {$product->name}. {$product->sales_count} sales in last 7 days",
                        'is_resolved' => false
                    ],
                    [
                        'severity' => 'medium',
                        'detected_at' => now()
                    ]
                );
            }

            Log::info('System alerts generated successfully');

        } catch (\Exception $e) {
            Log::error('Error generating system alerts: ' . $e->getMessage());
        }
    }

    public function loadAlerts()
    {
        $query = Anomaly::orderBy('detected_at', 'desc');

        if (!$this->showResolved) {
            $query->where('is_resolved', false);
        }

        if ($this->filter !== 'all') {
            $query->where('severity', $this->filter);
        }

        $this->alerts = $query->get();
    }

    public function resolveAlert($id)
    {
        try {
            $alert = Anomaly::find($id);
            if ($alert) {
                $alert->update(['is_resolved' => true]);
                $this->dispatch('alert-resolved');
                $this->loadAlerts();
                
                session()->flash('message', 'Alert marked as resolved');
            }
        } catch (\Exception $e) {
            Log::error('Error resolving alert: ' . $e->getMessage());
            session()->flash('error', 'Failed to resolve alert');
        }
    }

    public function deleteAlert($id)
    {
        try {
            Anomaly::destroy($id);
            $this->loadAlerts();
            session()->flash('message', 'Alert deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting alert: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete alert');
        }
    }

    public function refreshAlerts()
    {
        $this->generateSystemAlerts();
        $this->loadAlerts();
        session()->flash('message', 'Alerts refreshed successfully');
    }

    public function render()
    {
        return view('livewire.analytics.alerts-dashboard', [
            'criticalCount' => Anomaly::where('severity', 'critical')->where('is_resolved', false)->count(),
            'highCount' => Anomaly::where('severity', 'high')->where('is_resolved', false)->count(),
            'totalUnresolved' => Anomaly::where('is_resolved', false)->count(),
        ]);
    }
}
