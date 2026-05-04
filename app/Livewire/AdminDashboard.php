<?php

namespace App\Livewire;

use App\Models\Anomaly;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class AdminDashboard extends Component
{
    public int $totalProducts = 0;
    public float $totalStockValue = 0;
    public int $lowStockCount = 0;
    public int $pendingDeliveries = 0;

    // Month-over-month changes
    public float $productChangePercent = 0;
    public float $stockValueChangePercent = 0;
    public int $pendingDeliveriesThisWeek = 0;

    // Recent data
    public $recentProducts = [];
    public $recentActivity = [];

    public function mount(): void
    {
        $this->loadStats();
        $this->loadRecentData();
    }

    private function loadStats(): void
    {
        $now = Carbon::now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

        // ── Total Products ──────────────────────────────────────────────────
        $this->totalProducts = Product::count();
        $thisMonthProducts   = Product::where('created_at', '>=', $thisMonthStart)->count();
        $lastMonthProducts   = Product::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $this->productChangePercent = $lastMonthProducts > 0
            ? round((($thisMonthProducts - $lastMonthProducts) / $lastMonthProducts) * 100, 1)
            : ($thisMonthProducts > 0 ? 100 : 0);

        // ── Total Stock Value ────────────────────────────────────────────────
        $this->totalStockValue = (float) Product::sum(
            DB::raw('current_stock * price')
        );
        $lastMonthStockValue = (float) Product::sum(
            DB::raw('current_stock * price')
        ); // baseline same since we don't track historical stock snapshots
        // Show today's total sales value vs last month sales instead for comparison
        $thisMonthSales = Sale::where('sale_date', '>=', $thisMonthStart)->sum('total_amount');
        $lastMonthSales = Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
        $this->stockValueChangePercent = $lastMonthSales > 0
            ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($thisMonthSales > 0 ? 100 : 0);

        // ── Low Stock ────────────────────────────────────────────────────────
        // Uses the per-product threshold column added in migration
        $this->lowStockCount = Product::whereColumn('current_stock', '<=', 'low_stock_threshold')->count();

        // ── Pending Deliveries ───────────────────────────────────────────────
        $this->pendingDeliveries = Delivery::where('status', 'pending')->count();
        $this->pendingDeliveriesThisWeek = Delivery::where('status', 'pending')
            ->where('delivery_date', '>=', $now->copy()->startOfWeek())
            ->count();
    }

    private function loadRecentData(): void
    {
        $this->recentProducts = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        // Recent activity: mix anomalies + sales in descending order
        $anomalies = Anomaly::where('is_resolved', false)
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($a) => [
                'type'    => 'anomaly',
                'color'   => match ($a->severity) {
                    'critical' => 'red',
                    'high'     => 'orange',
                    'medium'   => 'amber',
                    default    => 'blue',
                },
                'title'   => ucfirst(str_replace('_', ' ', $a->type)),
                'body'    => \Illuminate\Support\Str::limit($a->description, 80),
                'time'    => $a->created_at->diffForHumans(),
            ]);

        $sales = Sale::with('product')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($s) => [
                'type'  => 'sale',
                'color' => 'green',
                'title' => 'Sale Recorded',
                'body'  => ($s->product?->name ?? 'Unknown') . ' — Qty: ' . $s->quantity . ' | PKR ' . number_format($s->total_amount),
                'time'  => $s->created_at->diffForHumans(),
            ]);

        $this->recentActivity = $anomalies->merge($sales)
            ->sortByDesc('time')
            ->values()
            ->take(5)
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
