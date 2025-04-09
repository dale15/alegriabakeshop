<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ProductIngredientRelationManager;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-s-squares-2x2';
    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Product Name')->required(),
                        TextInput::make('selling_price')->label('Selling Price')->numeric()->required(),
                        Select::make('category_id')->relationship('category', 'name'),
                        // TextInput::make('cost_price')->label('Cost')->numeric(),
                        Toggle::make('is_box')->label('Box'),
                    ])->columnSpan(2)->columns(2),
                Section::make('Image')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Image Upload')
                            ->directory('uploads')
                            ->preserveFilenames(),
                    ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Product Image')
                    ->size(100)
                    ->defaultImageUrl(asset('images/no_image.jpg')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric(decimalPlaces: 2)
                    ->money('PHP'),
                TextColumn::make('is_box')
                    ->label('Product Type')
                    ->badge()
                    ->color(fn($record): string => match (true) {
                        $record->is_box == 0 => 'info', // Not box
                        $record->is_box == 1 => 'primary', // Box
                        default => 'success', // default
                    })
                    ->getStateUsing(fn($record) => match (true) {
                        $record->is_box == 0 => 'Single Product', // Not box
                        $record->is_box == 1 => 'Box', // Box
                        default => 'In Stock',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            ProductIngredientRelationManager::class,
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
