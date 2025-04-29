<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ProductIngredientRelationManager;
use App\Models\Product;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

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
                        Select::make('category_id')->relationship('category', 'name')
                            ->createOptionForm([
                                TextInput::make('name')->label('Category Name')
                            ])
                            ->createOptionAction(function ($action) {
                                return $action
                                    ->after(function (Set $set, Get $get, $state) {
                                        Notification::make()
                                            ->title('Category Created')
                                            ->body('The category "' . $get('name') . '" has been added successfully.')
                                            ->success()
                                            ->send();
                                    });
                            }),
                        TextInput::make('selling_price')->label('Selling Price')->numeric()->required(),
                        TextInput::make('cost_price')->label('Cost')->numeric(),
                        TextInput::make('sku')->label('SKU'),

                    ])->columnSpan(2)->columns(2),
                Section::make('Meta')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Image Upload')
                            ->directory('uploads')
                            ->preserveFilenames(),
                        Toggle::make('is_box')->label('Box')->reactive(),
                        Select::make('allowed_items_ids')
                            ->label('Allowed Products in Box')
                            ->multiple()
                            ->options(fn() => Product::where('is_box', false)->pluck('name', 'id'))
                            ->visible(fn(Get $get) => $get('is_box') === true)
                            ->searchable()
                            ->preload(),
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
                Tables\Columns\TextColumn::make('sku')
                    ->sortable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->placeholder('No Category yet.'),
                Tables\Columns\TextColumn::make('cost_price')
                    ->numeric(decimalPlaces: 2)
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric(decimalPlaces: 2)
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('margin %')
                    ->label('Margin')
                    ->getStateUsing(function ($record) {
                        if ($record->selling_price == 0) {
                            return '0%';
                        }
                        $margin = (($record->selling_price - $record->cost_price) / $record->selling_price) * 100;
                        return number_format($margin, 2) . '%';
                    }),
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
                Filter::make('is_box')
                    ->label('Boxes')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('is_box', true))
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
