<?php

namespace App\Filament\Resources\ProductResource;

use App\Models\Product;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Milon\Barcode\DNS1D;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('barcode')
                    ->label('Bar kod')
                    ->unique('products', 'barcode', ignoreRecord: true)
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
                TextInput::make('price')->label('Sotish narxi')->numeric()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nomi')
                ->searchable(),
                Tables\Columns\TextColumn::make('barcode')->label('Bar kod')
                ->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Narxi'),
                Tables\Columns\TextColumn::make('barcode_image')
                    ->label('Bar kod')
                    ->html(fn ($record) => DNS1D::getBarcodeHTML($record->barcode, 'EAN13', 2, 50, 'black')
                    ),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('print_barcode')
                    ->label('Print Barcode')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Product $record) => route('product.barcode.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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


}
