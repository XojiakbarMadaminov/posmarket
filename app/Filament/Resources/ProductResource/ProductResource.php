<?php

namespace App\Filament\Resources\ProductResource;

use App\Models\Product;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Milon\Barcode\DNS1D;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getLabel(): ?string
    {
        return "Tovarlar";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('barcode')
                    ->label('Bar kod')
                    ->unique('products', 'barcode', ignoreRecord: true)
                    ->numeric()
                    ->required()
                    ->autofocus()
                    ->suffixAction(
                        Action::make('generateBarcode')
                            ->icon('heroicon-m-sparkles')
                            ->tooltip('EAN-13 Bar kod yaratish')
                            ->action(function (\Filament\Forms\Set $set) {
                                $set('barcode', self::generateEAN13Barcode());
                            })
                    ),
                TextInput::make('name')->label('Nomi')->required(),
                TextInput::make('initial_price')->label('Kelgan narxi')->numeric(),
                TextInput::make('price')
                    ->label('Sotish narxi')
                    ->numeric()
                    ->required()
                    ->rule(function (callable $get) {
                        $initial = $get('initial_price');

                        return function (string $attribute, $value, $fail) use ($initial) {
                            if ($initial !== null && $value <= $initial) {
                                $fail('Sotish narxi kelgan narxidan katta bo‘lishi kerak.');
                            }
                        };
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nomi')
                ->searchable(),
                Tables\Columns\TextColumn::make('barcode')->label('Bar kod')
                ->searchable(),
                Tables\Columns\TextColumn::make('initial_price')->label('Kelgan narxi'),
                Tables\Columns\TextColumn::make('price')->label('Narxi'),
                ViewColumn::make('barcode_image')
                    ->label('Bar kod')
                    ->view('filament.components.barcode'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([

                Tables\Actions\Action::make('print_barcode')
                    ->label('Print Barcode')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Product $record) => route('product.barcode.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_print_barcode')
                        ->label('Barcodeni chop etish')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();
                            $url = route('product.barcodes.bulk', ['ids' => $ids]);

                            // Redirect qilish uchun response qaytaramiz:
                            return redirect($url);
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()


                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    private static function generateEAN13Barcode(): string
    {
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= random_int(0, 9);
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $code[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $code . $checksum;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed(); // barcha ko‘rinadi
    }




}
