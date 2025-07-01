<?php

namespace App\Filament\Resources\DebtorResource;

use App\Filament\Resources\DebtorResource\RelationManagers\TransactionRelationManager;
use App\Models\Debtor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DebtorResource extends Resource
{
    protected static ?string $model = Debtor::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $label = 'Qarzdorlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([

                    TextInput::make('full_name')
                        ->label('To‘liq ism')
                        ->placeholder('Ism Familiya')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),

                    TextInput::make('phone')
                        ->label('Telefon raqam')
                        ->placeholder('+998 90 123 45 67')
                        ->required(),

                    Select::make('currency')
                        ->label('Valyuta')
                        ->options([
                            'uzs' => 'UZS (So‘m)',
                            'usd' => 'USD (Dollar)',
                        ])
                        ->default('uzs')
                        ->required(),


                    TextInput::make('amount')
                        ->label('Qarz summasi')
                        ->numeric()
                        ->placeholder('Masalan: 150 000')
                        ->required(),

                    DatePicker::make('date')
                        ->label('Qarz sanasi')
                        ->default(today())
                        ->required(),
                ]),

                Textarea::make('note')
                    ->label('Qo‘shimcha qaydlar')
                    ->placeholder('Masalan: Do‘kon tovarlari uchun...')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable()
                    ->label('To`liq ism'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Telefon nomer'),
                TextColumn::make('amount')
                    ->sortable()
                    ->label('Qarz summasi'),
                TextColumn::make('currency')
                    ->label('Valyuta'),
                TextColumn::make('date')
                    ->sortable()
                    ->label('Sana')
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
            ->actions([
                Action::make('add_debt')
                    ->label('Qarz qo‘shish')
                    ->color('danger')
                    ->form([
                        TextInput::make('amount')
                            ->label('Qarz summasi')
                            ->prefix(fn(Debtor $record) => $record->currency)
                            ->numeric()
                            ->required(),
                        Textarea::make('note')
                            ->label('Izoh')
                            ->nullable(),
                        DatePicker::make('date')
                            ->label('Sana')
                            ->default(today()),
                    ])
                    ->action(function (array $data, Debtor $record) {
                        // Bazaga yozish
                        $record->transactions()->create([
                            'type' => 'debt',
                            'amount' => $data['amount'],
                            'date' => $data['date'],
                            'note' => $data['note'] ?? null,
                        ]);

                        $record->increment('amount', $data['amount']); // total qarz yangilanadi
                    }),
                Action::make('add_payment')
                    ->label('To‘lov qilish')
                    ->color('success')
                    ->form(fn(Debtor $record) => [
                        TextInput::make('amount')
                            ->label('To‘lov summasi')
                            ->prefix($record->currency)
                            ->numeric()
                            ->required()
                            ->rule('lte:' . $record->amount) // `amount` dan katta bo‘lmasin
                            ->helperText('Maksimum: ' . $record->amount . ' ' . $record->currency),

                        DatePicker::make('date')
                            ->label('To‘lov sanasi')
                            ->default(today())
                            ->required(),

                        Textarea::make('note')
                            ->label('Izoh')
                            ->nullable(),
                    ])
                    ->action(function (array $data, Debtor $record) {
                        if ($data['amount'] > $record->amount) {
                            Notification::make()
                                ->title('To‘lov summasi mavjud qarzdan katta bo‘lishi mumkin emas')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->transactions()->create([
                            'type' => 'payment',
                            'amount' => $data['amount'],
                            'date' => $data['date'],
                            'note' => $data['note'] ?? null,
                        ]);

                        $record->decrement('amount', $data['amount']);
                    }),
                Action::make('view_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document')
                    ->url(fn ($record) => route('debtor.check.pdf', $record))
                    ->openUrlInNewTab()
                    ->color('gray'),

                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebtors::route('/'),
            'create' => Pages\CreateDebtor::route('/create'),
            'edit' => Pages\EditDebtor::route('/{record}/edit'),
            'view' => Pages\ViewDebtor::route('/{record}'),
        ];
    }
}
