<?php

namespace App\Filament\Resources\DebtorResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;

class TransactionRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';
    protected static ?string $title = 'Qarz/To‘lovlar tarixi';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Turi')
                    ->badge()
                    ->color(fn ($record) => $record->type === 'debt' ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state === 'debt' ? 'Qarz' : 'To‘lov'),

                TextColumn::make('amount')
                    ->label('Summasi')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Sana')
                    ->date('d-m-Y'),

                TextColumn::make('note')
                    ->label('Izoh')
                    ->limit(50)
                    ->wrap()
                    ->extraAttributes(['class' => 'text-blue-600 underline cursor-pointer'])
                    ->action(
                        Tables\Actions\Action::make('view_note')
                            ->label('Ko‘rish')
                            ->modalHeading('To‘liq izoh')
                            ->modalDescription(fn ($record) => $record->note ?? 'Izoh mavjud emas')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Yopish')
                    ),
            ])
            ->defaultSort('date', 'desc');
    }
}
