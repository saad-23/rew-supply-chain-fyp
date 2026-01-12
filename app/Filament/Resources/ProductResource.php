<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Details')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    
                    Forms\Components\TextInput::make('sku')
                        ->label('SKU / Barcode')
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('current_stock')
                            ->numeric()
                            ->label('Quantity in Stock')
                            ->required(),
                        
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('PKR') // Adds currency symbol
                            ->required(),
                    ]),
                ])
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('sku')
                ->label('SKU')
                ->copyable() // Lets admin copy ID with one click
                ->searchable(),

            Tables\Columns\TextColumn::make('current_stock')
                ->sortable()
                // Logic: If stock < 10, show RED. If > 10, show GREEN.
                ->color(fn (string $state): string => $state < 10 ? 'danger' : 'success') 
                ->label('Stock Status'),

            Tables\Columns\TextColumn::make('price')
                ->money('PKR')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->date(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
}
