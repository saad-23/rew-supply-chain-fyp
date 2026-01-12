<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DemandForecastChart extends ChartWidget
{
    protected static ?string $heading = 'AI Demand Forecast (Next 6 Months)';
    
    // Position: 2 means it sits below the Stats widget (if you have one)
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        // =========================================================
        // 🔮 FUTURE PYTHON API INTEGRATION AREA
        // =========================================================
        // When you are ready for real AI, you will uncomment this:
        // 
        // $response = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:5000/predict');
        // $aiData = $response->json(); 
        // 
        // For now, we use DUMMY DATA to visualize the Requirement:
        // =========================================================

        $dummyData = [50, 80, 65, 120, 90, 150]; // Predicted units sold
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        return [
            'datasets' => [
                [
                    'label' => 'Predicted Demand (AI Model)',
                    'data' => $dummyData, // <--- This uses the dummy data
                    'borderColor' => '#9333ea', // Purple (AI color)
                    'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Makes the line curved/smooth
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // A line chart is best for forecasting trends
    }
}