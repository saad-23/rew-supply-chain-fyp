<?php

namespace App\Livewire\Analytics;

use App\Models\Anomaly;
use App\Services\AnomalyDetectionService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class AnomalyMonitor extends Component
{
    use WithPagination;

    public function scan(AnomalyDetectionService $service)
    {
        $count = $service->scanForAnomalies();
        if ($count > 0) {
            session()->flash('message', "$count new anomalies detected.");
        } else {
            session()->flash('message', "No new anomalies found.");
        }
    }

    public function resolve($id)
    {
        $anomaly = Anomaly::find($id);
        if ($anomaly) {
            (new AnomalyDetectionService)->resolve($anomaly);
            session()->flash('message', "Anomaly marked as resolved.");
        }
    }

    public function render()
    {
        return view('livewire.analytics.anomaly-monitor', [
            'anomalies' => Anomaly::orderBy('is_resolved', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
