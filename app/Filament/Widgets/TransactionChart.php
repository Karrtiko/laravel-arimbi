<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionChart extends ChartWidget
{
    protected ?string $heading = 'Transactions per Day (Last 30 Days)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Order::query()
            ->selectRaw("strftime('%Y-%m-%d', created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates
        $chartData = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $record = $data->firstWhere('date', $date);
            $chartData[] = $record ? $record->count : 0;
            $labels[] = Carbon::parse($date)->format('M d');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Transactions',
                    'data' => $chartData,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // blue-500
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
