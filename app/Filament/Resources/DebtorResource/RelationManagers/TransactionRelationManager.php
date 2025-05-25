<?php

namespace App\Filament\Resources\DebtorResource\RelationManagers;

use App\Models\Debtor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_debt')
                ->label('Qarz qo‘shish')
                ->color('danger')
                ->icon('heroicon-m-plus')
                ->form([
                    TextInput::make('amount')
                        ->label('Qarz summasi')
                        ->numeric()
                        ->required(),
                    Textarea::make('note')
                        ->label('Izoh')
                        ->nullable(),
                    DatePicker::make('date')
                        ->label('Sana')
                        ->default(today()),
                ])
                ->action(function (array $data) {
                    $this->ownerRecord->transactions()->create([
                        'type' => 'debt',
                        'amount' => $data['amount'],
                        'date' => $data['date'],
                        'note' => $data['note'],
                    ]);

                    $this->ownerRecord->increment('amount', $data['amount']);
                }),

            Action::make('add_payment')
                ->label('To‘lov qilish')
                ->color('success')
                ->icon('heroicon-m-cash')
                ->form([
                    TextInput::make('amount')
                        ->label('To‘lov summasi')
                        ->numeric()
                        ->required(),
                    DatePicker::make('date')
                        ->label('To‘lov sanasi')
                        ->default(today()),
                    Textarea::make('note')
                        ->label('Izoh')
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $this->ownerRecord->transactions()->create([
                        'type' => 'payment',
                        'amount' => $data['amount'],
                        'date' => $data['date'],
                        'note' => $data['note'],
                    ]);

                    $this->ownerRecord->decrement('amount', $data['amount']);
                }),
        ];
    }
}
