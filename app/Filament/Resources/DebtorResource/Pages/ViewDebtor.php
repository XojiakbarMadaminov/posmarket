<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource\DebtorResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewDebtor extends ViewRecord
{
    protected static string $resource = DebtorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Chop etish')
                ->icon('heroicon-m-printer')
                ->color('gray')
                ->url(fn () => route('debtor.check.pdf', $this->record))
                ->openUrlInNewTab()
        ];
    }
}
