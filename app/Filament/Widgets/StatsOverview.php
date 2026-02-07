<?php

namespace App\Filament\Widgets;

use App\Models\Bundle;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('All products in database')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            Stat::make('Total Orders', Order::count()) // Replaced categories with orders
                ->description('Total transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            Stat::make('Total Revenue', 'Rp ' . number_format(Order::sum('total_price'), 0, ',', '.')) // Added revenue
                ->description('Gross revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
