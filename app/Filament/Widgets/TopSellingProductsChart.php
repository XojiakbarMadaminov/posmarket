<?php

namespace App\Filament\Widgets;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Carbon;
use App\Models\SaleItem;

class TopSellingProductsChart extends BarChartWidget
{
    use InteractsWithForms;

    public ?string $start_date = null;
    public ?string $end_date   = null;

    protected static ?string $heading = 'Top 10 sotilgan tovarlar';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    /** --------- FILTER FORM --------- */
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('start_date')
                ->label('Boshlanish sanasi')
                ->default(now())
                ->reactive()
                ->closeOnDateSelection(),

            DatePicker::make('end_date')
                ->label('Tugash sanasi')
                ->default(now())
                ->reactive()
                ->closeOnDateSelection(),
        ];
    }

    /** --------- CHART MA’LUMOTI --------- */
    protected function getData(): array
    {
        $start = Carbon::parse($this->start_date ?? now())->startOfDay();
        $end   = Carbon::parse($this->end_date   ?? now())->endOfDay();

        $topProducts = SaleItem::with('product')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('product_id, SUM(qty) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sotilgan soni',
                    'data'  => $topProducts->pluck('total_qty'),
                ],
            ],
            'labels'   => $topProducts->pluck('product.name')->toArray(),
        ];
    }
}
