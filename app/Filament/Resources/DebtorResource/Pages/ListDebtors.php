<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource\DebtorResource;
use App\Filament\Widgets\DebtorStatsOverview;
use App\Models\Debtor;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDebtors extends ListRecords
{
    protected static string $resource = DebtorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DebtorStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Qarzdorlar' => Tab::make(__('Qarzdorlar'))->badge(Debtor::scopes('stillInDebt')->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->scopes('stillInDebt')),
            'Qarzdorlik yopilganlar' => Tab::make(__('Qarzdorlik yopilganlar'))->badge(Debtor::scopes('zeroDebt')->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->scopes('zeroDebt')),
        ];
    }
}
