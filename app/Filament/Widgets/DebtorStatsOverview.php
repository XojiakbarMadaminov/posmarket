<?php

namespace App\Filament\Widgets;

use App\Models\Debtor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class DebtorStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $debts = Debtor::query()
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->pluck('total', 'currency');

        return [
            Card::make("Qarzdorlik (UZS)", number_format($debts['uzs'] ?? 0, 0, '.', ' ') . " so'm"),
            Card::make("Qarzdorlik (USD)", number_format($debts['usd'] ?? 0, 2) . " $"),
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
