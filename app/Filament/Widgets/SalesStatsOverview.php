<?php

namespace App\Filament\Widgets;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;

class SalesStatsOverview extends BaseWidget
{
    use InteractsWithForms;

    // Form mavjud bo‘lishi uchun public propertilar
    public ?string $start_date = null;
    public ?string $end_date   = null;

    /** --------- FILTER FORM --------- */
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('start_date')
                ->label('Boshlanish sanasi')
                ->default(now())          // bugungi kun
                ->reactive()
                ->closeOnDateSelection(),

            DatePicker::make('end_date')
                ->label('Tugash sanasi')
                ->default(now())
                ->reactive()
                ->closeOnDateSelection(),
        ];
    }

    /** --------- STATLAR --------- */
    protected function getCards(): array
    {
        // Fallback: foydalanuvchi tanlamasa ham bugungi sana
        $start = Carbon::parse($this->start_date ?? now())->startOfDay();
        $end   = Carbon::parse($this->end_date   ?? now())->endOfDay();

        $sales       = Sale::whereBetween('created_at', [$start, $end])->get();
        $totalSales  = $sales->sum('total');

        $totalProfit = SaleItem::whereIn('sale_items.sale_id', $sales->pluck('id'))
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('COALESCE(SUM( (sale_items.price - products.initial_price) * sale_items.qty ), 0) AS profit')
            ->value('profit');


        return [
            Card::make('Umumiy Sotuvlar', number_format($totalSales, 2) . " so'm"),
            Card::make('Foyda', number_format($totalProfit, 2) . " so'm"),
        ];
    }
}
